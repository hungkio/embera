<table border="1" cellpadding="5" cellspacing="0">
    <thead>
    <tr>
        <th>Order Number</th>
        <th>Payment Order ID</th>
        <th>Reason for Payment Failure</th>
        <th>User ID</th>
        <th>Rent out from</th>
        <th>Return to</th>
        <th>When to rent</th>
        <th>When to Return</th>
        <th>Merchant ID</th>
        <th>Name of Merchant</th>
        <th>Order bills（VND）</th>
        <th>Order fees（VND）</th>
        <th>Status of Order</th>
        <th>Staff ID</th>
        <th>Staff name</th>
        <th>Order comes from</th>
        <th>When to pay</th>
        <th>Payment Channel</th>
        <th>Status of Refund</th>
        <th>Refund</th>
        <th>Refund Fees</th>
        <th>Accured revenue to Dealer</th>
        <th>Accured revenue to Merchant</th>
        <th>Billing Strategy</th>
        <th>Location</th>
        <th>Region</th>
        <th>City</th>
        <th>Area</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $row)
        <tr>
            <td>{{ $row->order_number }}</td>
            <td>{{ $row->payment_id }}</td>
            <td>{{ $row->payment_failure_reason }}</td>
            <td>{{ $row->user_id }}</td>
            <td>{{ $row->rental_shop }}</td>
            <td>{{ $row->return_shop }}</td>
            <td>{{ formatDate($row->rental_time) }}</td>
            <td>{{ formatDate($row->return_time) }}</td>
            <td>{{ $row->merchant_id }}</td>
            <td>{{ $row->merchant_name }}</td>
            <td>{{ number_format($row->order_amount, 0, '.', ',') }}</td>
            <td>{{ number_format($row->fees, 0, '.', ',') }}</td>
            <td>{{ $row->order_status }}</td>
            <td>{{ $row->employee_id }}</td>
            <td>{{ $row->employee_name }}</td>
            <td>{{ $row->order_source }}</td>
            <td>{{ formatDate($row->payment_time) }}</td>
            <td>{{ $row->payment_channels }}</td>
            <td>{{ $row->refund_status }}</td>
            <td>{{ number_format($row->refund_amount, 0, '.', ',') }}</td>
            <td>{{ number_format($row->refund_fee, 0, '.', ',') }}</td>
            <td>{{ number_format($row->revenue_to_dealer, 0, '.', ',') }}</td>
            <td>{{ number_format($row->revenue_to_merchant, 0, '.', ',') }}</td>
            <td>{{ $row->charging_strategy }}</td>
            <td>{{ $row->rental_shop_address }}</td>
            <td>{{ $row->region }}</td>
            <td>{{ $row->city }}</td>
            <td>{{ $row->area }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
