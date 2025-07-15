<form action="{{ $url }}" method="POST" id="shop-form" enctype="multipart/form-data">
    @csrf
    @method($method ?? 'POST')

    <x-card>
        <fieldset>
            <legend class="font-weight-semibold text-uppercase font-size-sm">
                {{ __('Thông tin cửa hàng') }}
            </legend>

            <x-text-field name="shop_name" :label="__('Tên cửa hàng')" :value="old('shop_name', $shop->shop_name ?? '')" readonly />
            <x-text-field name="address" :label="__('Địa chỉ')" :value="old('address', $shop->address ?? '')" readonly />
            <x-text-field name="contract_customer" :label="__('Tên khách hàng')" :value="$contract->customer_name ?? '-'" readonly />
            <x-text-field name="customer_position" :label="__('Chức vụ khách hàng')" :value="$contract->customer_position ?? '-'" readonly />

            {{-- Danh sách thiết bị (chỉ hiển thị, không chỉnh sửa) --}}
            <div class="form-group mt-4">
                <label class="font-weight-bold">{{ __('Danh sách thiết bị') }}</label>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="thead-light">
                        <tr>
                            <th>Tên</th>
                            <th>Mã máy</th>
                            <th>Đơn vị</th>
                            <th>Số lượng</th>
                            <th>Ghi chú</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                        $grouped = [];

                        foreach ($deviceSummary as $device) {
                        $name = strtoupper(trim($device['name'] ?? '-'));
                        $code = trim($device['code'] ?? '');
                        $pin = (int)($device['pin'] ?? 0);
                        $note = $device['note'] ?? '';

                        if (!$name) continue;

                        if (!isset($grouped[$name])) {
                        $grouped[$name] = [
                        'codes' => [],
                        'total_pin' => 0,
                        'count' => 0,
                        'note' => $note,
                        ];
                        }

                        if ($code) {
                        $grouped[$name]['codes'][] = $code;
                        }

                        $grouped[$name]['total_pin'] += $pin;
                        $grouped[$name]['count']++;
                        }
                        @endphp

                        @forelse ($grouped as $deviceName => $row)
                        @php
                        $chunks = array_chunk(array_unique($row['codes']), 4);
                        $codes = collect($chunks)->map(fn($chunk) => implode(', ', $chunk))->implode('<br>');
                        @endphp
                        <tr>
                            <td>{{ $deviceName }}</td>
                            <td>{!! $codes !!}</td>
                            <td>{{ $row['total_pin'] }}</td>
                            <td>{{ $row['count'] }}</td>
                            <td>{{ $row['note'] }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5">Không có thiết bị</td></tr>
                        @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Editable sản phẩm --}}
            <div class="form-group mt-5">
                <label class="font-weight-bold">{{ __('Danh sách sản phẩm') }}</label>
                <div class="col-lg-12">
                    <div id="product-container">
                        @forelse ($productSummary as $index => $product)
                        <div class="product-entry d-flex mb-2">
                            <input type="text" name="product_name[]" class="form-control mr-2" value="{{ $product['name'] ?? '' }}" placeholder="Tên sản phẩm" required>
                            <input type="text" name="product_code[]" class="form-control mr-2" value="{{ $product['code'] ?? '' }}" placeholder="Mã sản phẩm" >
                            <input type="text" name="product_unit[]" class="form-control mr-2" value="{{ $product['unit'] ?? '' }}" placeholder="Đơn vị">
                            <input type="number" name="product_quantity[]" class="form-control mr-2" value="{{ $product['quantity'] ?? 0 }}" placeholder="Số lượng">
                            <input type="text" name="product_note[]" class="form-control mr-2" value="{{ $product['note'] ?? '' }}" placeholder="Ghi chú">
                            <button type="button" class="btn btn-danger btn-sm remove-product">–</button>
                        </div>
                        @empty
                        <div class="product-entry d-flex mb-2">
                            <input type="text" name="product_name[]" class="form-control mr-2" placeholder="Tên sản phẩm" required>
                            <input type="text" name="product_code[]" class="form-control mr-2" placeholder="Mã sản phẩm" >
                            <input type="text" name="product_unit[]" class="form-control mr-2" value="Cái" placeholder="Đơn vị">
                            <input type="number" name="product_quantity[]" class="form-control mr-2" placeholder="Số lượng">
                            <input type="text" name="product_note[]" class="form-control mr-2" placeholder="Ghi chú">
                            <button type="button" class="btn btn-danger btn-sm remove-product">–</button>
                        </div>
                        @endforelse
                    </div>
                    <button type="button" class="btn btn-sm btn-primary mt-2" id="add-product">+ Thêm sản phẩm</button>
                </div>
            </div>

            {{-- Hidden JSON input chỉ cho sản phẩm --}}
            <input type="hidden" name="product_json" id="product_json">
            <div class="form-group row mt-4">
                <label for="bbnt_file" class="col-lg-2 col-form-label text-lg-right">
                    <span class="text-danger">*</span> {{ __('File biên bản nghiệm thu') }}
                </label>
                <div class="col-lg-9">
                    @if(!empty($shop->bbnt_file))
                    <p>
                        📎 <strong>File hiện tại:</strong>
                        <a href="{{ asset('storage/' . $shop->bbnt_file) }}" target="_blank">
                            {{ basename($shop->bbnt_file) }}
                        </a>
                    </p>
                    @endif

                    <input type="file" name="bbnt_file" id="bbnt_file"
                           class="form-control inputfile"
                           accept=".pdf,.docx">

                    <small class="form-text text-muted">
                        Chỉ chấp nhận file PDF hoặc DOCX. Nếu chọn file mới, file cũ sẽ bị thay thế.
                    </small>

                    @error('bbnt_file')
                    <span class="form-text text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

        </fieldset>
    </x-card>

    <div class="d-flex justify-content-center mt-4">
        <button type="submit" class="btn btn-primary ml-2">
            <i class="fal fa-save mr-1"></i> {{ __('Lưu BBNT') }}
        </button>

        @if(isset($shop->id))
        <a href="{{ route('admin.shops.bbnt.download', $shop) }}" target="_blank" class="btn btn-secondary ml-2">
            <i class="fal fa-print mr-1"></i> In BBNT
        </a>
        @endif
    </div>

</form>

@push('js')
<script>
    function generateProductJSON() {
        let products = [];
        document.querySelectorAll('#product-container .product-entry').forEach(entry => {
            const name = entry.querySelector('[name="product_name[]"]').value;
            const code = entry.querySelector('[name="product_code[]"]').value ?? '';
            const unit = entry.querySelector('[name="product_unit[]"]').value;
            const quantity = entry.querySelector('[name="product_quantity[]"]').value;
            const note = entry.querySelector('[name="product_note[]"]').value;

            if (name) {
                products.push({
                    name,
                    code,
                    unit: unit || '',
                    quantity: parseInt(quantity) || 0,
                    note: note || ''
                });
            }
        });
        document.getElementById('product_json').value = JSON.stringify({ products });
        console.log('Generated product_json:', document.getElementById('product_json').value); // Debug
    }

    // Thêm mới sản phẩm
    document.getElementById('add-product').addEventListener('click', function () {
        const container = document.getElementById('product-container');
        const entry = container.querySelector('.product-entry').cloneNode(true);
        entry.querySelectorAll('input').forEach(input => {
            input.value = input.name === 'product_unit[]' ? '' : '';
        });
        container.appendChild(entry);
        console.log('Added new product-entry'); // Debug
    });

    // Xóa sản phẩm
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-product')) {
            const entry = e.target.closest('.product-entry');
            entry.remove();
            console.log('Removed product-entry'); // Debug
        }
    });

    // Handle form submission via AJAX
    document.getElementById('shop-form').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default form submission (page reload)
        generateProductJSON(); // Generate product_json before submission

        const form = this;
        const formData = new FormData(form); // Collect form data, including files
        formData.append('_method', form.querySelector('input[name="_method"]').value); // Add method override for PUT

        fetch(form.action, {
            method: 'POST', // Always use POST, as Laravel handles _method for PUT/PATCH
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, // CS laravel-csrf-token
                'Accept': 'application/json', // Ensure server returns JSON
            },
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'Server error');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Lưu BBNT thành công!'); // Success message
                // Optionally reload form data or reset form
                // form.reset(); // Uncomment to reset form
                // Optionally reload the page content without full reload
                window.location.reload(); // Soft reload to refresh form data
            } else {
                alert('Lưu BBNT thất bại: ' + (data.message || 'Lỗi không xác định'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi: ' + error.message);
        });
    });
    document.getElementById('shop-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fal fa-spinner fa-spin mr-1"></i> Đang lưu...';
        // ... rest of the code ...
        // In .then and .catch, re-enable button:
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fal fa-save mr-1"></i> Lưu BBNT';
    });
</script>
@endpush
