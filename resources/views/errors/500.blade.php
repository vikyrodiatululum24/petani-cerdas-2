@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('image', '/assets/500.png')
@section('message', __('Server Error'))
