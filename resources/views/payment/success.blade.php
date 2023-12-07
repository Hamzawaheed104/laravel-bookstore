@extends('layouts.app')

@section('content')
  <div class="flex items-center justify-center h-screen">
    <div class="max-w-md w-full p-6 bg-white rounded-md shadow-md">
      <div class="text-center mb-6">
        <svg class="text-green-500 w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M5 13l4 4L19 7"></path>
        </svg>
        <h2 class="text-xl font-semibold text-gray-800">Payment Successful</h2>
      </div>

      <div class="text-center mb-4">
        <p class="text-gray-600">Thank you purchasing!</p>
      </div>

      <div class="text-center">
        <a href="{{ route('dashboard') }}"
          class="bg-blue-500 hover:bg-blue-600 text-white rounded-md px-4 py-2">Go to Dashboard</a>
      </div>
    </div>
  </div>
@endsection
