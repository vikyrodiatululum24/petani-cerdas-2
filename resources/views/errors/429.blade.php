@extends('errors::minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('image', '/assets/429.png')
@section('message', __('Too Many Requests'))
