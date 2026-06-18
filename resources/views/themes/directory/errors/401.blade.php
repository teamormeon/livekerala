@extends('layouts.error')
@section('title', trans('401 Unauthorized'))

@section('error_code','401')
@section('error_message', trans("You are a unauthorized user"))

