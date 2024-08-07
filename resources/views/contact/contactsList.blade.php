@extends('app.layouts.app')
@section('title','contactUs Page')
@section('content')
    @push('styles')
        {{--        <link rel="stylesheet" href="{{asset('css/about.css')}}">--}}
    @endpush
    {{--        {{dd($allContacts)}}--}}
    <div class="container mt-5 d-flex col-12 mb-5  flex-column">
        <div>
            <h1 class="d-flex justify-content-center">Contacts list</h1>
            <div class="d-flex justify-content-end m-3">
                <!-- Button trigger modal -->
                <a type="button" class="btn btn-primary btn-rounded mx-3" data-mdb-toggle="modal"
                   data-mdb-target="#exampleModal">Import</a>
                <a href="{{url('/export/contact')}}" type="button" class="btn btn-success btn-rounded">Export</a>
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-body p-4">
                                <!-- Pills content -->
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="pills-login" role="tabpanel"
                                         aria-labelledby="mdb-tab-login">
                                        <form action="{{url('/import/contact')}}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <label class="form-label" for="customFile">Please select a file....</label>
                                            <div class="form-outline mb-4">
                                                <input type="file" name="file" class="form-control" id="customFile"/>
                                            </div>
                                            <!-- Submit button -->
                                            <button type="submit" class="btn btn-primary btn-block mb-4" style="">
                                                submit
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('errors'))
            <div class="alert alert-danger">
                {{ session('errors') }}
            </div>
        @endif
        @if (session('errors-import'))
            <div class="alert alert-danger">
                <ul>
                    @foreach (session('errors-import') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <table class="table align-middle mb-0 bg-white">
            <thead class="bg-light">
            <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>City</th>
                <th>Message</th>
                <th style="text-align: center;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($allContacts as $contact)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img
                                src="https://mdbootstrap.com/img/new/avatars/8.jpg"
                                class="rounded-circle"
                                alt=""
                                style="width: 45px; height: 45px"
                            />
                            <div class="ms-3">
                                <p class="fw-bold mb-1">{{$contact['name']}}</p>
                                <p class="text-muted mb-0">{{$contact['email']}}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="fw-normal mb-1">{{$contact['phone']}}</p>
                    </td>
                    <td>
                        <span class="fw-normal mb-1">{{$contact['city']}}</span>
                    </td>
                    <td>
                        <p>{{$contact['message']}}</p>
                    </td>
                    <td style="text-align: center;">
                        <a
                            href="{{url('contact/edit/'.$contact['id'])}}"
                            type="button"
                            class="btn btn-link btn-rounded btn-sm fw-bold"
                            data-mdb-ripple-color="dark"
                        >
                            Update
                        </a>
                        <a
                            href="{{url('contact-delete/'.$contact['id'])}}"
                            type="button"
                            class="btn btn-link btn-rounded btn-sm fw-bold"
                            data-mdb-ripple-color="dark"
                        >
                            Delete
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection
