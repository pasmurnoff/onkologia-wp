@php
    $page_id = isset($page_id) ? $page_id : get_the_ID();

    $rows = [
        ['label' => 'Название организации', 'value' => get_field('org_name', $page_id)],
        ['label' => 'Юридический адрес организации', 'value' => get_field('org_address', $page_id)],
        ['label' => 'ИНН', 'value' => get_field('inn', $page_id)],
        ['label' => 'КПП', 'value' => get_field('kpp', $page_id)],
        ['label' => 'ОГРН/ОГРНИП', 'value' => get_field('ogrn', $page_id)],
        ['label' => 'Расчетный счёт', 'value' => get_field('rs', $page_id)],
        ['label' => 'Банк', 'value' => get_field('bank_name', $page_id)],
        ['label' => 'ИНН банка', 'value' => get_field('bank_inn', $page_id)],
        ['label' => 'БИК банка', 'value' => get_field('bik', $page_id)],
        ['label' => 'Корреспондентский счёт банка', 'value' => get_field('ks', $page_id)],
        ['label' => 'Юридический адрес банка', 'value' => get_field('bank_address', $page_id)],
    ];
@endphp

<div class="rec-card">
    <div class="list">
        @foreach ($rows as $row)
            @if (!empty($row['value']))
                <div class="row">
                    <div class="label">{{ $row['label'] }}</div>
                    <div class="value">{!! nl2br(e($row['value'])) !!}</div>
                </div>
            @endif
        @endforeach
        <a class="btn_primary perevod" data-modal-open="donate">Выполнить перевод</a>
    </div>
</div>
