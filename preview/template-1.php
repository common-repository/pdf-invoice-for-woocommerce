<!DOCTYPE html>
<html>
<head>
	<title>Invoice No: 10002</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<style>
    @page {
        margin:10px;
    }
    body {
        margin-top:15px;
        background:#ffffff;
        font-size:16px;
        font-weight:400;
        line-height:22px;
        color: #212529;
        -webkit-text-size-adjust: 100%;
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        font-family: Helvetica, sans-serif;
    }
    .invoice{
        background: #fff;
        padding: 20px;
    }
    table.table-main,
    table.tab-foot{
        width:100%;
    }
    table.table-main > tr > td{
        width:50%;
    }
    table.table-main tr td span{
        display:block;
    }
    .txt-r{
        text-align:right;
    }
    .ft22{
        font-size:22px;
    }
    .ft20{
        font-size:20px;
    }
    .ft18{
        font-size:18px;
    }
    .ft16{
        font-size:16px;
    }
    table.table-main,
    table.items{
        width:100%;
        border-spacing:0;
        border-collapse:collapse;
        page-break-inside: avoid;
    }
    table.tab-foot{
        width:100%;
        border-spacing:0;
        page-break-inside: avoid;
    }
    table.items{
        margin-top:15px;
        border-top: 1px solid #687173;
        border-bottom: 1px solid #687173;
    }
    table.items thead tr th{
        background:#EAECED;
        padding:6px 0;
    }
    table.items thead tr th,
    table.items tbody tr td{
        border-collapse: collapse;
        margin:2px 0;
    }
    table.items thead tr th.no,
    table.items tbody tr td.no{
        width:5%;
    }
    table.items thead tr th.title,
    table.items tbody tr td.title{
        width:50%;
        text-align:left;
    }
    table.items thead tr th.qty,
    table.items tbody tr td.qty,
    table.items thead tr th.unit,
    table.items tbody tr td.unit,
    table.items thead tr th.total,
    table.items tbody tr td.total{
        width:15%;
    }
    table.items tbody tr td.no,
    table.items tbody tr td.qty,
    table.items tbody tr td.unit,
    table.items tbody tr td.total{
        text-align:center;
    }
    table.items tbody tr td.title span.desc{
        color:#687173;
        font-size: 12px;
    }
    table.items tbody tr:nth-child(even){
        background-color:#EEEEEE;
    }
    table.tab-foot tbody tr td.foot-sp{
        width:56%;
    }
    table.tab-foot tbody tr td.foot-st{
        width:22%;
        border-bottom: 1px solid #687173;
        font-size:18px;
        padding:6px 0 6px 12px;
    }
    .invoice-footer {
        border-top: 1px solid #ddd;
        padding-top: 10px;
        font-size: 10px;
    }
    .invoice-footer p{
        padding:0;
        margin:0;
        line-height:16px;
    }
    .text-center{
        text-align:center;
    }
    .in-btm{
        padding-right:8px;
    }
    .inb{
        font-weight:600;
        text-transform:capitalize;
    }
    .in-label{
        display: inline-block;
        padding: 6px 24px;
        margin: 6px 0;
        border-radius: 6px;
    }
    .in-paid{
        background:#28a745;
        color:#ffffff;
    }
    .in-due{
        background:#ffc107;
        color:#343a40;
    }
    strong{
        padding-left:4px;
    }
    table.table-main tr td span.woocommerce-Price-amount,
    table.table-main tr td span.amount,
    table.table-main tr td span.woocommerce-Price-currencySymbol{
        display:inline;
    }
    table.table-main tr td span.woocommerce-Price-currencySymbol{
        font-family:DejaVu Sans,Helvetica,sans-serif;
    }
</style>
<body>
<div class="invoice">
	<table class="table-main">
		<tr>
            <td>{invoice_logo}</td>
			<td class="txt-r ft22">INVOICE</td>
		</tr>
		<tr>
			<td></td>
            <td class="txt-r">
                <span class="ft18 inb">{your_company_name}</span>
                <span class="ft16">{your_company_address}</span>
                <span class="ft16">{your_company_phone}</span>
                <span class="ft16">{your_company_email}</span>
            </td>
		</tr>
		<tr>
			<td>
				<span class="ft22">BILL TO</span>
				<span class="ft18 inb">Blue Sky Marketing Limited</span>
				<span class="ft16">45, Terminal Road, Park Street<br/>Near Skyline Flyover<br/>New York, NY 10007</span>
				<span class="ft16">9876543210</span>
				<span class="ft16">skyline@gmail.com</span>
				<span class="ft16 inb"></span>
			</td>
			<td class="txt-r">
				<span class="ft18 inb">Invoice No: 10002</span>
				<span class="ft16">Order Number: 101</span>
				<span class="ft16">Invoice Date: 27/07/2022</span>

				<span class="ft18 inb"><h4 class="in-label in-paid">PAID</h4></span>
				<span class="ft20">Amount Due: <?php echo wc_price(0.00); ?></span>
			</td>

		</tr>
		<tr>
			<td colspan="2">
				<table class="items">
					<thead>
					<tr>
						<th class="no">#</th>
						<th class="title">Item</th>
						<th class="qty">Qty</th>
						<th class="unit">Unit Price</th>
						<th class="total">Total</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td class="no">1</td>
						<td class="title">
							<span>T-Shirt</span>
							<span class="desc">SKU: woo-tshirt</span>                                </td>
						<td class="qty">4</td>
						<td class="unit"><?php echo wc_price(18.00); ?></td>
						<td class="total"><?php echo wc_price(72.00); ?></td>
					</tr>
					<tr>
						<td class="no">2</td>
						<td class="title">
							<span>Beanie</span>
							<span class="desc">SKU: woo-beanie</span>                                </td>
						<td class="qty">8</td>
						<td class="unit"><?php echo wc_price(18.00); ?></td>
						<td class="total"><?php echo wc_price(144.00); ?></td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="text-r">
				<table class="tab-foot">
					<tbody>
					<tr>
						<td class="foot-sp"></td>
						<td class="foot-st">Subtotal:</td>
						<td class="foot-st"><?php echo wc_price(216.00); ?></td></tr>
					<tr>
					<tr>
						<td class="foot-sp"></td>
						<td class="foot-st">Shipping:</td>
						<td class="foot-st">Free shipping</td></tr>
					<tr>
					<tr>
						<td class="foot-sp"></td>
						<td class="foot-st">Payment method:</td>
						<td class="foot-st">Cash on delivery</td></tr>
					<tr>
					<tr>
						<td class="foot-sp"></td>
						<td class="foot-st">Total:</td>
						<td class="foot-st"><?php echo wc_price(216.00); ?></td></tr>
					<tr>
					<tr>
						<td class="foot-sp"></td>
						<td class="foot-st">Amount Due:</td>
						<td class="foot-st"><?php echo wc_price(0.00); ?></td></tr>
					<tr>
					</tbody>
				</table>
			</td>
		</tr>
	</table>
	<div class="invoice-footer">
        <p class="text-center">{invoice_footer_note}</p>
        <p class="text-center">{invoice_copyright_note}</p>
	</div>
</div>
<script type='text/php'>
  if(isset($pdf)){
    $x = 530;
    $y = 820;
    $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
    $font = $fontMetrics->get_font("helvetica","bold");
    $size = 8;
    $color = array(0,0,0);
    $word_space = 0.0;
    $char_space = 0.0;
    $angle = 0.0;
    $pdf->page_text($x,$y,$text,$font,$size,$color,$word_space,$char_space,$angle);
  }
</script>
</body>
</html>
