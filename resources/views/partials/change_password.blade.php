<style>
    .card-header{
        font-size: 17px;
    }
</style>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="card-header font-weight-bold">Change Password</div>
            <form action="{{ route('password.change', Auth::user()->id) }}" method="POST">
                @csrf
                <div class="form-group mt-3 col row">
                    <div class="col">
                        <label for="password">New Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>                    
                    <div class="col">
                        <label for="password">Confirm Password</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>
    
                <div class="form-group col">
                    <button type="submit" class="btn btn-info">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>