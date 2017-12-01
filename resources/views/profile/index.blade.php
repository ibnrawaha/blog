@extends('layouts.app')

@section('content')

	<h1>User Profile</h1>
	
	{{-- `id`, `user_id`, `full_name`, `job`, `degree`, `about`, `phone`, `address`, `city`, `country`, `zip_code`, `created_at`, `updated_at`SELECT * FROM `profiles` WHERE 1 --}}

	<form action="{{ route('profile.update' , $user->name) }}" class="form-group col-md-6 " method="post">
		{!! csrf_field() !!}
		{{ method_field('PUT') }}

		<label for="FullName" >Full Name</label>
		<input type="text" name="full_name" class="form-control" value="{{$profile->full_name}}" >

		<label for="Email" >Email</label>
		<input type="text" name="email" class="form-control" value="{{$user->email}}" disabled>

		<label for="Phone" >Phone</label>
		<input type="text" name="phone" class="form-control" value="{{$profile->phone}}">

		<label for="Address" >Address</label>
		<input type="text" name="address" class="form-control" value="{{$profile->address}}">

		<label for="city" >City</label>
		<input type="text" name="city" class="form-control" value="{{$profile->city}}">

		<label for="country" >Country</label>
		<input type="text" name="country" class="form-control" value="{{$profile->country}}">

		<label for="zip_code" >Zip Code</label>
		<input type="text" name="zip_code" class="form-control" value="{{$profile->zip_code}}">

		<label for="degree" >Degree</label>
		<input type="text" name="degree" class="form-control" value="{{$profile->degree}}">
		
		<label for="Job" >Job</label>
		<input type="text" name="job" class="form-control" value="{{$profile->job}}">

		<label for="about" >About Me</label>
		{{-- <input type="text" name="about" class="form-control" value="{{$profile->about}}"> --}}
		<textarea name="about" id="" cols="30" rows="10" class="form-control">{{$profile->about}}</textarea>
		

		{{-- <label for="ChangePassword" >Change Password</label>
		<input type="password" name="password" class="form-control" placeholder="Change Your Password"> --}}

		<br><br>
		<button type="submit" name="submit" class="btn btn-primary form-control">Update Your Profile</button>
	</form>

	
@endsection