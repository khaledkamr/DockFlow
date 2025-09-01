@extends('layouts.print')

@section('title', 'عقد عمل')

@section('content')
<h5 class="text-center mb-4">عقد خدمات لوجستية</h5>
حرر هذا العقد بالتوافق والتراضي بين الأطراف الموضحة معلوماتهم أدناه:-
<ul class="small">
    <li>
        <span class="fw-bold">{{ $company->name }} - </span> سجل تجاري {{ $company->CR }} - عنوانها {{ $company->national_address }} - رقم ضريبي {{ $company->TIN }}
        ويمثلها بالتوقيع على العقد بصفته {{ $contract->company_representative_role }} {{ $contract->company_representative }} {{ $contract->company_representative_nationality }} الجنسية
        بموجب سجل مدني {{ $contract->company_representative_NID }} او من ينوب عنه ويشار اليه بالطرف الأول في هذا العقد.
    </li>
    <li>
        <span class="fw-bold">{{ $contract->customer->name }} - </span> سجل تجاري {{ $contract->customer->CR }} - عنوانها {{ $contract->customer->national_address }} - رقم ضريبي {{ $contract->customer->TIN }}
        ويمثلها بالتوقيع على العقد بصفته {{ $contract->customer_representative_role }} {{ $contract->customer_representative }} {{ $contract->customer_representative_nationality }} الجنسية
        بموجب سجل مدني {{ $contract->customer_representative_NID }} او من ينوب عنه ويشار اليه بالطرف الثاني في هذا العقد.
    </li>
    <li>
        <span class="fw-bold">تمهيد:</span>
        <div>
            يرغب الطرف الثاني في تسير أعماله على اكمل وجه في سوق النقل السعودي الامر الذي يتطلب منه توفير خدمة تخزين الحاويات في المنطقة اللوجستية لميناء الدمام
        </div>
        <div>
            وعليه سيلتزم الطرف الأول بتوفير الخدمات المذكورة أعلاه للطرف الثاني على اكمل وجه حسب الفرص المتاحة لديه ووفقا للأسعار المتفق عليها والموضحة ادناه.
        </div>
    </li>
    <li class="mt-3">
        <span class="fw-bold">الخدمات والأسعار:</span>
        <div>
            <table class="table table-bordered">
                <thead class="text-center">
                    <tr class="table-primary">
                        <th>الرقم</th>
                        <th>الوصف</th>
                        <th>السعر</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($contract->services as $index => $service)
                        <tr>
                            <td>{{ $index + 1 }} </td>
                            <td>{{ $service->description }} </td>
                            <td>{{ $service->pivot->price == 0 ? 'مجاناً' : $service->pivot->price }} </td>
                            <td>{{ $service->pivot->unit .' '. $service->pivot->unit_desc }} </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </li>
    <li>
        <span class="fw-bold">الشروط والأحكام:</span>
        <div><strong>1-</strong> يجب على الطرف الثاني اشعار الطرف الأول بحاجته الى الخدمة المطلوبة وذلك عبر الايميل او وسائل الاتصال خلال مدة 24 ساعة.</div>
        <div><strong>2-</strong> يجب على الطرف الثاني تجهيز المساحة المتوفرة لديه بحيث يتم تخزين الحاويات بالساحة وقت وصولها.</div>
        <div><strong>3-</strong> يلتزم الطرف الثاني بسداد فواتير الطرف الأول خلال 7 أيام من تقديم الفاتورة.</div>
        <div><strong>4-</strong> يسري هذا العقد لمدة {{ $months }} أشهر وفي حالة الرغبة في تمديد المدة بالتوافق بين الطرفين يظل هذا العقد ساري وإذا استحدثت شروط يتم تعديل العقد في حينة او يعتمد ما يتم الاتفاق عليه من خلال الإيميلات الرسمية بين الطرفين.</div>
        <div><strong>5-</strong> الأسعار الموضحة لا تشمل الضريبة او رسوم حكومية اخرى.</div>
    </li>
</ul>

<div class="d-flex justify-content-around">
    <div>
        <div class="small fw-bold">الطرف الأول</div>
        <div class="small fw-bold">{{ $company->name }} </div>
        <div class="fw-bold" style="font-size: 10px;">الختم:</div>
        <div class="fw-bold" style="font-size: 10px;">التوقيع:</div>
    </div>
    <div>
        <div class="small fw-bold">الطرف الثاني</div>
        <div class="small fw-bold">{{ $contract->customer->name }} </div>
        <div class="fw-bold" style="font-size: 10px;">الختم:</div>
        <div class="fw-bold" style="font-size: 10px;">التوقيع:</div>
    </div>
</div>

@endsection