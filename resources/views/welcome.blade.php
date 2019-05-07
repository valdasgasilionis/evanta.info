@extends('layouts.app')
@section('content')  
    <div class="container"> 
    {{-- error rendering --}}     
        <div>
            @if (session('success message'))
                <div class="alert alert-success">
                    {{session ('success message')}}
                </div>            
            @endif
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li> {{ $error }}</li>                                
                        @endforeach
                    </ul>
                </div>                    
            @endif
        </div>
    {{-- navigation bar --}}          

        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/rents">Availability</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gallery">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/documentation">Documentation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact us</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
@endsection  

