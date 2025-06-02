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
        <th>Merchant ID rent out from</th>
        <th>Merchant rent out from</th>
        <th>Merchant return to</th>
        <th>Renting time</th>
        <th>Order bills</th>
        <th>Order bills（VND）</th>
        <th>Commission fees</th>
        <th>Commission fees（VND）</th>
        <th>Status of Order</th>
        <th>Order belongs to</th>
        <th>merchant ID</th>
        <th>Name of Merchant</th>
        <th>Staff ID</th>
        <th>Staff name</th>
        <th>Order comes from</th>
        <th>When to pay</th>
        <th>Payment Channel</th>
        <th>Status of Refund</th>
        <th>Refund</th>
        <th>Commission of Refunds</th>
        <th>Profit-sharing to dealer</th>
        <th>Accured revenue to Dealer</th>
        <th>Accured revenue to Merchant</th>
        <th>Billing Strategy</th>
        <th>Shop name</th>
        <th>Shop type</th>
        <th>Location</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $row)
        <tr>
            <td>{{ $row->order_number }}</td>
            <td>{{ $row->payment_order_id }}</td>
            <td>{{ $row->reason_for_payment_failure }}</td>
            <td>{{ $row->user_id }}</td>
            <td>{{ $row->rent_out_from }}</td>
            <td>{{ $row->return_to }}</td>
            <td>{{ $row->when_to_rent }}</td>
            <td>{{ $row->when_to_return }}</td>
            <td>{{ $row->merchant_id_rent_out_from }}</td>
            <td>{{ $row->merchant_rent_out_from }}</td>
            <td>{{ $row->merchant_return_to }}</td>
            <td>{{ $row->renting_time }}</td>
            <td>{{ $row->order_bills }}</td>
            <td>{{ $row->order_bills_vnd }}</td>
            <td>{{ $row->commission_fees }}</td>
            <td>{{ $row->commission_fees_vnd }}</td>
            <td>{{ $row->status_of_order }}</td>
            <td>{{ $row->order_belongs_to }}</td>
            <td>{{ $row->merchant_id }}</td>
            <td>{{ $row->name_of_merchant }}</td>
            <td>{{ $row->staff_id }}</td>
            <td>{{ $row->staff_name }}</td>
            <td>{{ $row->order_comes_from }}</td>
            <td>{{ optional($row->when_to_pay)->format('Y-m-d H:i:s') }}</td>
            <td>{{ $row->payment_channel }}</td>
            <td>{{ $row->status_of_refund }}</td>
            <td>{{ $row->refund }}</td>
            <td>{{ $row->commission_of_refunds }}</td>
            <td>{{ $row->profit_sharing_to_dealer }}</td>
            <td>{{ $row->revenue_to_dealer }}</td>
            <td>{{ $row->revenue_to_merchant }}</td>
            <td>{{ $row->billing_strategy }}</td>
            <td>{{ $row->shop_name }}</td>
            <td>{{ $row->shop_type }}</td>
            <td>{{ $row->location }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
