@if(in_array(\Illuminate\Support\Str::lower($status), ['дубль','неверный номер']))
<span
 class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium leading-5 bg-black text-white">
  {{ $status }}
</span>
@elseif($inStatus = \App\CRM\Status::whereName($status)->first())
<span
 class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium leading-5 bg-{{$inStatus->color}}-100 text-{{$inStatus->color}}-800">
  {{ $status }}
</span>
@else
<span
 class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium leading-5 bg-gray-100 text-gray-800">
  {{ $status ?? 'Новый' }}
</span>
@endif
