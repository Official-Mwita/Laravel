@extends('app')

@section('content')
    <div>
        @if ($orders->count())
            <table>
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Type</th>
                        <th>Topic</th>
                        <th>Date Due</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->type_of_paper }}</td>
                            <td>{{ $order->subject_name }}</td>
                            <td>{{ $order->created_at->addHours($order->hours)->toDateString() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $orders->links() }}
        @else
            <div style="text-align: center">
                <h2 style="color: red"> No orders please! </h2>
                <p>Tomorrow may come brighter</p>
            </div>
        @endif
    </div>
@endsection
