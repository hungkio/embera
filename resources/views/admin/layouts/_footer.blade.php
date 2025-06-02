<div class="navbar navbar-expand-lg navbar-light">
    <div class="text-center d-lg-none w-100">
        <button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target="#navbar-footer">
            <i class="icon-unfold mr-2"></i>
            {{ __('Chân trang') }}
        </button>
    </div>

    <div class="navbar-collapse collapse" id="navbar-footer">
        <span class="navbar-text">
            &copy; 2020 - {{ date('Y', strtotime(now())) }} - {{ __('Được phát triển và duy trì bởi') }} <a href="https://lapo.vn" target="_blank" style="color: #1989fa !important;">LAPO.VN</a>
        </span>
    </div>
</div>
