<?php
/**
 * WHMCS Admin paneli için Döviz / Altın kurları listeleme 
 * Geliştiren: Murat Tahtacı / Domainhizmetleri.com
 * 03.09.2019
 */
add_hook('AdminHomeWidgets', 1, function() {
    return new DovizWidget();
});

setlocale(LC_MONETARY, 'tr_TR');
ini_set('default_socket_timeout', 3);

class DovizWidget extends \WHMCS\Module\AbstractWidget
{
    protected $title = 'Döviz Kurları';
    protected $description = '';
    protected $weight = 40;
    protected $columns = 1;
    protected $cache = true;
    protected $cacheExpiry = 3600;
    protected $requiredPermission = '';

    public function getData()
    {
		$connect_web_doviz = file_get_contents('https://api.canlidoviz.com/items/current?marketId=0&code=USD&code=EUR');
    	$json_doviz = json_decode($connect_web_doviz, true);
		$usd_buying = money_format('%.4n',$json_doviz[1]['data']['lastBuyPrice']);
		$usd_selling = money_format('%.4n',$json_doviz[1]['data']['lastSellPrice']);
		$euro_buying = money_format('%.4n',$json_doviz[0]['data']['lastBuyPrice']);
		$euro_selling = money_format('%.4n',$json_doviz[0]['data']['lastSellPrice']);
		
		$connect_web_altin = file_get_contents('https://api.canlidoviz.com/items/latest-data?marketId=0&type=GOLD');
		$json_altin = json_decode($connect_web_altin, true);
		$altin_ceyrek_satis = money_format('%.4n',$json_altin[0]['data']['lastSellPrice']);
		$altin_gram_satis = money_format('%.4n',$json_altin[9]['data']['lastSellPrice']);
		
		return array(
            'usd_buying' => $usd_buying,
            'usd_selling' => $usd_selling,
			'euro_buying' => $euro_buying,
			'euro_selling' => $euro_selling,
			'altin_ceyrek_satis' => $altin_ceyrek_satis,
			'altin_gram_satis' => $altin_gram_satis,
        );
    }

    public function generateOutput($data)
    {

        return <<<EOF
    <div class="col-sm-6 bordered-right" style="border-right: 1px solid #eee;">
        <div class="item" style="padding: 13px 0;">
            <div class="data color-green"><i class="fas fa-dollar-sign color-green"></i> <strong>{$data['usd_buying']}</strong></div>
            <div class="note">  USD Alış</div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="item" style="padding: 13px 0;">
            <div class="data color-green"><i class="fas fa-dollar-sign color-green"></i> <strong>{$data['usd_selling']}</strong></div>
            <div class="note">  USD Satış</div>
        </div>
    </div>
    <div class="col-sm-6 bordered-right bordered-top" style="border-right: 1px solid #eee;    border-top: 1px solid #eee;">
        <div class="item" style="padding: 13px 0;">
            <div class="data color-pink"><i class="fas fa-euro-sign color-pink"></i> <strong>{$data['euro_buying']}</strong></div>
            <div class="note">  Euro Alış</div>
        </div>
    </div>
    <div class="col-sm-6 bordered-top" style="    border-top: 1px solid #eee;">
        <div class="item" style="padding: 13px 0;">
            <div class="data color-pink"><i class="fas fa-euro-sign color-pink"></i> <strong>{$data['euro_selling']}</strong></div>
            <div class="note">  Euro Satış</div>
        </div>
    </div>
	<div class="col-sm-6 bordered-right bordered-top" style="border-right: 1px solid #eee;    border-top: 1px solid #eee;">
        <div class="item" style="padding: 13px 0;">
            <div class="data"><i class="fas fa-burn"></i> {$data['altin_gram_satis']}</div>
            <div class="note">  Gram Altın Satış</div>
        </div>
    </div>
    <div class="col-sm-6 bordered-top" style="    border-top: 1px solid #eee;">
        <div class="item" style="padding: 13px 0;">
            <div class="data"><i class="fas fa-burn"></i> {$data['altin_ceyrek_satis']}</div>
            <div class="note">  Çeyrek Altın Satış</div>
        </div>
    </div>
	<style>
	.widget-dovizwidget .item div {float:left;padding-bottom: 11px;padding-top: 0px;}
	.widget-dovizwidget .item div:nth-child(2) {    padding-left: 5px;}
	.widget-dovizwidget .item .note {font-size: 12px;    padding-top: 1px;}

	</style>
EOF;
    }
}
