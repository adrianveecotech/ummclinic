<style>
    .card-header{
        font-size: 17px;
    }
    small{
        color: red;
    }
</style>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            @foreach($profiles as $profile)
            <form action="@if(Auth::user()->type == "clinic") {{ url('clinic/profile/update', $profile->id) }} @endif @if(Auth::user()->type == "company") {{ url('company/profile/update', $profile->id) }} @endif" method="POST">
                @csrf
                <div class="card-header font-weight-bold">Edit Profile</div>
                <div class="form-group mt-3 col">
                    <label for="name"><strong>Name</strong></label>
                    <input type="text" class="form-control" name="name" value="{{ $profile->name }}">
                </div>            
                <div class="form-group mt-3 col">
                    <label for="email"><strong>Email</strong> <small>*This is your login email</small></label>
                    <input type="email" class="form-control" name="email" value="{{ $profile->email }}">
                </div>            
                <div class="form-group mt-3 col">
                    <label for="contact"><strong>Contact</strong></label>
                    <input type="text" class="form-control" name="contact" value="{{ $profile->contact }}">
                </div>            
                <div class="form-group mt-3 col">
                    <label for="address"><strong>Address</strong></label>
                    <textarea type="text" class="form-control" name="address" rows="2">{{ $profile->address }}</textarea>
                </div>
                <div class="form-group mt-3 col">
                    <button type="submit" class="btn btn-info">Update Profile</button>
                </div>
            </form>
            @endforeach
        </div>
    </div>
</div>