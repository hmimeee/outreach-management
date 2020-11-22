<p>Guest Post Invoice No: {{$invoice->name}}<br/>
---------------------<br/>
No of Guest Posts: {{$invoice->backlinks->count()}}<br/>
---------------------<br/>
Per Guest Post Price (Each): {{$invoice->site->post_price}}<br/>
---------------------<br/>
Guest Post Url:<br/>
---------------------<br/>
@foreach($invoice->backlinks as $link)
{{$link->backlink}}<br/>
{{$link->url}}<br/>
---------------------<br/>
@endforeach