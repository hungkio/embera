<?php

namespace App\Http\Controllers\Admin;

use App\Domain\MailSetting\Models\MailSetting;
use App\Models\GmailAccount;
use App\Models\GmailAttachment;
use App\Models\GmailMessage;
use App\Services\GmailService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GmailController
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('view', MailSetting::class);

        $account = $this->account();
        $messages = $account?->messages()->paginate(20);
        $selectedMessage = null;

        if ($account && $request->filled('message')) {
            $selectedMessage = $account->messages()->whereKey($request->integer('message'))->first();
        }

        if (!$selectedMessage && $messages && $messages->count() > 0) {
            $selectedMessage = $messages->first();
        }

        return view('admin.gmail.index', [
            'isConfigured' => app(GmailService::class)->isConfigured(),
            'account' => $account,
            'messages' => $messages,
            'selectedMessage' => $selectedMessage,
        ]);
    }

    public function connect(GmailService $gmailService): RedirectResponse
    {
        $this->authorize('view', MailSetting::class);

        $state = Str::random(40);
        session(['gmail_oauth_state' => $state]);

        return redirect()->away($gmailService->getAuthorizationUrl($state));
    }

    public function callback(Request $request, GmailService $gmailService): RedirectResponse
    {
        $this->authorize('view', MailSetting::class);

        abort_unless(
            $request->filled('code')
                && hash_equals((string) session('gmail_oauth_state'), (string) $request->string('state')),
            403
        );

        try {
            $tokens = $gmailService->exchangeCodeForTokens((string) $request->string('code'));
            $profile = $gmailService->fetchProfile($tokens['access_token']);
            $account = GmailAccount::firstOrNew(['admin_id' => auth()->id()]);

            $account->fill([
                'email' => Arr::get($profile, 'emailAddress'),
                'access_token' => $tokens['access_token'],
                'refresh_token' => Arr::get($tokens, 'refresh_token', $account->refresh_token),
                'token_type' => Arr::get($tokens, 'token_type'),
                'scopes' => $gmailService->scopes(),
                'expires_at' => now()->addSeconds((int) Arr::get($tokens, 'expires_in', 3600)),
            ]);
            $account->save();

            $gmailService->syncRecentMessages($account, 5, true);

            flash()->success(__('Kết nối Gmail thành công.'));
        } catch (\Throwable $exception) {
            report($exception);
            flash()->error($exception->getMessage());
        } finally {
            session()->forget('gmail_oauth_state');
        }

        return redirect()->route('admin.gmail.index');
    }

    public function sync(GmailService $gmailService): RedirectResponse
    {
        $this->authorize('view', MailSetting::class);

        $account = $this->account();
        abort_unless($account, 404);

        try {
            $synced = $gmailService->syncRecentMessages($account, 5, true);
            flash()->success(__('Đã đồng bộ :count email gần nhất.', ['count' => $synced]));
        } catch (\Throwable $exception) {
            report($exception);
            flash()->error($exception->getMessage());
        }

        return redirect()->route('admin.gmail.index');
    }

    public function disconnect(): RedirectResponse
    {
        $this->authorize('view', MailSetting::class);

        $account = $this->account();
        abort_unless($account, 404);

        GmailMessage::where('gmail_account_id', $account->id)->delete();
        $account->delete();

        flash()->success(__('Đã ngắt kết nối Gmail.'));

        return redirect()->route('admin.gmail.index');
    }


    public function importDailyOrdersToday(Request $request): RedirectResponse
    {
        $this->authorize('view', MailSetting::class);

        $account = $this->account();
        abort_unless($account, 404);

        $dateStr = $request->input('date');
        $importDate = $dateStr ? Carbon::parse($dateStr)->toDateString() : Carbon::yesterday('Asia/Ho_Chi_Minh')->toDateString();

        try {
            $exitCode = Artisan::call('gmail:import-daily-orders', [
                '--date' => $importDate,
            ]);

            if ($exitCode === 0) {
                flash()->success(__('Đã chạy import daily orders cho ngày :date.', ['date' => Carbon::parse($importDate)->format('d/m/Y')]));
            } else {
                flash()->error(__('Import daily orders kết thúc với mã lỗi :code.', ['code' => $exitCode]));
            }
        } catch (\Throwable $exception) {
            report($exception);
            flash()->error($exception->getMessage());
        }

        return redirect()->route('admin.gmail.index');
    }


    private function account(): ?GmailAccount
    {
        return GmailAccount::where('admin_id', auth()->id())->first();
    }
}
