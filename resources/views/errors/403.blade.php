@extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('image', '/assets/403.png')
@section('message', __($exception->getMessage() ?: 'Forbidden'))
