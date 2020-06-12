<?php
include_once('Bitgo.php');
include_once('wallet_config.php');


$bitgo = new Bitgo(BITGO_ACCESS_KEY, COIN, TESTNET);

//echo json_encode($bitgo->ping());
//echo json_encode($bitgo->generateWallet('gist', 'gist'));
//echo json_encode($bitgo->getTransfer('5d896baec930717103fdf5521ff16320', 'f51c39f45333bbf2865c8ed401b43aff1f2a3aee6ca98b1630792ac31235d863'));
//echo json_encode($bitgo->getWallet('5d8e5de8b3c32e4506e045775384bf31'));
//echo json_encode($bitgo->getWalletByAddress('2MxtQdAEf1dFTN8884swpAdYzXS5xPF5nTg'));
//echo json_encode($bitgo->addWalletWebhook('5d8cfde502c34c7d031fcc2b9decd186', WEBHOOK_URL, 0));
//echo json_encode($bitgo->feeEstimate());
//echo json_encode($bitgo->getEstimateTxFee(400));
//echo json_encode($bitgo->getWalletByAddress(COMPANY_ADDRESS));
echo json_encode($bitgo->verifyAddress(COMPANY_ADDRESS));
