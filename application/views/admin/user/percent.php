

<script>
    window.onload = function() {
        <?php foreach ($users as $user): ?>
            var chart<?= $user['id'] ?> = new CanvasJS.Chart("chartContainer-<?= $user['id'] ?>", {
                animationEnabled: true,
                title: {
                    text: "<?= $user['surname'] ?>"
                },
                data: [{
                    type: "pie",
                    startAngle: 240,
                    yValueFormatString: "##0.00\"%\"",
                    indexLabel: "{label} {y}",
                    dataPoints: [
                        <?php foreach (array_slice($userPercent[$user['id']], 0, count($userPercent[$user['id']]) - 1) as $percent): ?>
                            {y: <?= $percent['count']/$userPercent[$user['id']]['all'] * 100 ?>, label: "<?= $percent['name']." (".$percent['count']."позиций)" ?>"},
                        <?php endforeach; ?>
                    ]
                }]
            });
            chart<?= $user['id'] ?>.render();
        <?php endforeach;?>
    }
</script>

<div class="flex_box">
    <?php foreach ($users as $user): ?>
        <div class="in_flex_50">
            <div id="chartContainer-<?= $user['id'] ?>" style="height: 370px; width: 100%;"></div>
        </div>
    <?php endforeach;?>
</div>


<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>