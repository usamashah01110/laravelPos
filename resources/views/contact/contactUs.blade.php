@extends('app.layouts.app')
@section('title','contactUs Page')
@section('content')
    @push('styles')
{{--        <link rel="stylesheet" href="{{asset('css/about.css')}}">--}}
    @endpush

    <div class="container mt-5 d-flex col-6 mb-5  flex-column">
{{--        {{dd($allContacts->first()['name'])}}--}}
        <h1 class="d-flex justify-content-center">Contact Us</h1>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{url('/contact')}}" class="row g-3" method="post">
            @csrf
            <div class="col-md-6">
                <label for="inputName" class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" id="inputName">
                @if ($errors->has('name'))
                    <span class="text-danger" id="contactNameError">{{ $errors->first('name') }}</span>
                @endif
            </div>
            <div class="col-md-6">
                <label for="inputEmail4" class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control"  id="inputEmail4">
                @if ($errors->has('email'))
                    <span class="text-danger" id="contactEmailError">{{ $errors->first('email') }}</span>
                @endif
            </div>
            <div class="col-6">
                <label for="inputPhone" class="form-label">phone</label>
                <input type="tel" name="phone" value="{{old('phone') }}" class="form-control" id="inputPhone" >
                @if ($errors->has('phone'))
                    <span class="text-danger" id="phoneError">{{ $errors->first('phone') }}</span>
                @endif
            </div>
            <div class="col-md-6">
                <label for="inputCity4" class="form-label">City</label>
                <input type="text" name="city" value="{{old('city') }}" class="form-control"  id="inputCity4">
                @if ($errors->has('city'))
                    <span class="text-danger" id="contactCityError">{{ $errors->first('city') }}</span>
                @endif
            </div>
            <div class="col-12">
                <label for="validationTextarea" class="form-label">message</label>
                <textarea rows="4" class="form-control" name="message" id="validationTextarea"  style="resize: none;">{{old('message') }}</textarea>
                @if ($errors->has('message'))
                    <span class="text-danger" id="messageError">{{ $errors->first('message') }}</span>
                @endif
            </div>
            <div class="d-grid gap-2 col-6 mx-auto">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

@endsection
