<div id="addressLayerContent">
    <table class="table table-cols">
        <colgroup>
            <col class="width5p">
            <col class="width15p">
        </colgroup>
        <thead>
        <tr>
            <th>선택</th>
            <th>배송지이름</th>
            <th>받으실분</th>
            <th>주 소</th>
            <th>연락처</th>
            <th>배송 메세지</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($shippingInfo as $info) : ?>
            <tr>
            <td><input type="radio" name="shippingSno" value="<?=$info['sno']?>"></td>
            <td><?php if ($info['defaultFl'] == 'y'): ?> (기본배송지) <br><?php endif;?><?=$info['shippingTitle']?></td>
            <td><?=$info['shippingName']?></td>
            <td><?=$info['shippingZonecode']?> <?=$info['shippingAddress']?> <?=$info['shippingAddressSub']?></td>
            <td> 전화번호 : <?=$info['shippingPhone']?> <br/> 휴대폰 : <?=$info['shippingCellPhone']?></td>
            <td><?=$info['shippingMessage']?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="center"><?= $page->getPage(); ?></div>
    <div class="text-center">
        <button type="submit" class="btn btn-lg btn-black js-change-shipping-address">저장</button>
    </div>
</div>
<script>
    // 저장 클릭
    $(document).on('click', '.js-change-shipping-address', function (e) {
        const selectedShipping = $('input[name="shippingSno"]:checked').val();

        if (!selectedShipping) {
            alert('배송지를 선택해주세요.');
            return;
        }

        const postData = {
            mode: 'change_shipping_address',
            shippingSno: $('input[name="shippingSno"]:checked').val(),
            applyNo: <?=$applyNo?>
        };

        $.post('../order/layer_regular_order_ps.php', postData, function (data) {
            if (data.result === 'success') {
                location.reload();
            } else {
                alert(data.message);
                setTimeout(function() {
                    $('#addressLayerContent').load('/order/layer_regular_order.php?mode=change-shipping-address&applyNo=<?=$applyNo?>&memNo=<?=$memNo?>');
                }, 2000);
            }
        });
    });

    // 페이징
    $(document).on('click', '.center a', function(e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        $.ajax({
            url: '/order/layer_regular_order.php?page='+page,
            type: 'GET',
            success: function(response) {
                $('#addressLayerContent').html($('<div>').html(response).find('#addressLayerContent').html());
            },
            error: function() {
                alert('페이지를 불러오는데 실패했습니다.');
            }
        });
    });
</script>
