<?php
// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=report.csv');
// Load the Magento core
require_once 'app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$userModel = Mage::getModel('admin/user');
$userModel->setUserId(0);

// Load the product collection

$collection = Mage::getModel('catalog/product')
 ->getCollection()
 ->addAttributeToSelect('*') //Select everything from the table
 ->addUrlRewrite(); //Generate nice URLs

/*
 For this example I am generating a CSV file,
 but you can change this to suit your needs.
*/
 // create a file pointer connected to the output stream
$output = fopen('php://output', 'w');
// output the column headings
fputcsv($output, array('title', 'sku', 'id', 'url'));

foreach($collection as $product) {
 //Load the product categories
 $categories = $product->getCategoryIds();
 //Select the last category in the list
 $categoryId = end($categories);
 
 //Load that category
 $category = Mage::getModel('catalog/category')->load($categoryId);
 // Collect details in variables
 $title=$product->getName();
 $sku=$product->getSku();
 $id=$product->getId();
 $url=str_replace('export.php/','',Mage::getBaseUrl()).$product->getUrlPath($category);
 fputcsv($output, array($title, $sku, $id, $url));
}
?>