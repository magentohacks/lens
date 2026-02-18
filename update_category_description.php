<?php
use Magento\Framework\App\Bootstrap;

require __DIR__ . '/app/bootstrap.php';

$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);
$objectManager = $bootstrap->getObjectManager();

/** Set area code */
$state = $objectManager->get(\Magento\Framework\App\State::class);
try {
    $state->setAreaCode('adminhtml');
} catch (\Exception $e) {
    // already set
}

/** CONFIG */
$categoryId = 44;

/** Your FULL HTML content */
$description = <<<HTML
<h4 class="content" style="text-align: justify;"><span style="font-size: large;">Wearing Daily Disposable Contact Lenses Are Safe, Buy Them At Modest Prices</span></h4>
<p class="content" style="text-align: justify;"><span style="font-size: small;">When it comes to <b>Buy Online </b>the quality based, the name of <b>Lens saver </b>comes first in the mind. There would be many of you using these lenses or wish to buy the new one, then you have landed at the right platform.</span></p>
<h4 class="content" style="text-align: justify;"><span style="font-size: large;">A bit about Daily Disposable Contact Lenses</span></h4>
<p class="content" style="text-align: justify;"><span style="font-size: small;">As the name itself depicts that it has been designed in a way so they are only for single use. They can be discarded at the end of each day. And you are allowed to carry a fresh pair of lenses to the eyes the next morning. Actually, they are high in demand as they serve great comfort to your eyes. In simple words, they are good for the health of the eyes.</span></p>
<p class="content" style="text-align: justify;"><span style="font-size: small;"><br /> As per the study, the more frequently you change your contact lenses, the more comfortable and relax you serve to your eyes. Actually, there is a need to understand that there are various things like lipids, calcium, protein and other substance get collected on your lenses and it is not considered safe for eyes. If you do not replace them time-to-time, the chance of getting your eyes infected increased.</span></p>
<p></p>
<h4 class="content" style="text-align: justify;"><span style="font-size: large;">Advantage of Daily Disposable Contact Lenses</span></h4>
<p><span><br /></span></p>
<ul>
<li>&bull; There is no need to do cleaning as you are allowed to throw them after using it.</li>
<li>&bull; They are safe to wear as there is no day-to-day accumulation of protein and other substances cause of eye infection.</li>
<li>&bull; The experts also advise them. If you are thinking that it would be costly to buy then you are a bit wrong as we serving you it at discounted prices.</li>
</ul>
<h4 class="content" style="text-align: justify;"><span style="font-size: large;">What We Serve You</span></h4>
<p class="content" style="text-align: justify;"><span style="font-size: small;">At this platform, we bring a wide array of <b>Contact Lenses Online </b>right from <b>Johnson And Johnson, Alcon Contact Lenses, Acuvue Lenses, Focus Contact Lense </b>to <b>Softlens. </b>If you have been looking for them then your search gets ended at this point which is indulged to serve you at the<b> Best Price.</b></span></p>
<p></p>
<h4 class="content" style="text-align: justify;"><span style="font-size: large;">A Wide Variety Of Daily Disposable Contact Lenses</span></h4>
<p><span><br /></span></p>
<ul>
<li><a href="https://lenssaver.co.uk/daily/1-day-acuvue-moist-30" target="_blank" rel="noopener">1 Day ACUVUE MOIST 30</a></li>
<li><a href="https://lenssaver.co.uk/contact-lenses/shop-by-manufacturer/dailies-aqua-comfort-plus-30" target="_blank" rel="noopener">Focus Dailies Aqua Comfort Plus30</a></li>
<li><a href="https://lenssaver.co.uk/contact-lenses/shop-by-manufacturer/acuvue-moist-1-day-for-astigmatism" target="_blank" rel="noopener">1 Day ACUVUE MOIST for Astigmatism</a></li>
<li><a href="https://lenssaver.co.uk/dailies-aquacomfort-plus-toric-30.html">Dailies® AquaComfort Plus® Toric 30 Pack</a></li>
<li><a href="https://lenssaver.co.uk/dailies-total-1-90-pack-90-lenses.html" target="_blank" rel="noopener">Dailies Total 1 90 Pack</a></li>
<li><a href="https://lenssaver.co.uk/1-day-acuvue-moist90-90-lenses.html" target="_blank" rel="noopener">1 Day Acuvue Moist 90</a></li>
<li><a href="https://lenssaver.co.uk/acuvue-oasys-max-1-day-astigmatism.html" target="_blank" rel="noopener">Acuvue oasys Max 1 day astigmatism </a></li>
<li><a href="https://lenssaver.co.uk/contact-lenses/shop-by-manufacturer/baush-lomb-ultra-one-day-30-pack" target="_blank" rel="noopener">Bausch and Lomb Ultra one day</a></li>
<li><a href="https://lenssaver.co.uk/daily/clariti-1day-multifocal" target="_blank" rel="noopener">Clariti 1 day Multifocal</a></li>
<li><a href="https://lenssaver.co.uk/precision-1.html" target="_blank" rel="noopener">Precision 1 day contact lenses</a></li>
</ul>
<p class="content" style="text-align: justify;"><span style="font-size: small;">The above-mentioned products are available at <b>Discounted Price </b>in<b> Uk, United Kingdom. </b>If you are one of them love to take care of your eyes and never buy the product that does not hold quality, then you must choose us to<b> Shop Now.</b> <b>At Lens saver</b>, we are also serving you <b>Free Shipping. </b>What are you waiting for? You must step ahead to <b>Order Contact Lenses Online </b>with<b> Boxes.</b></span></p>
<p><span><br /><br /></span></p>
HTML;

/** Services */
$categoryRepository = $objectManager->get(\Magento\Catalog\Api\CategoryRepositoryInterface::class);
$storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);

/** Load category in default scope (ALL STORES) */
$category = $categoryRepository->get($categoryId, 0);
$category->setStoreId(0); // 0 = All Store Views
$category->setDescription($description);

/** Save */
$categoryRepository->save($category);

echo "✅ Category {$categoryId} description updated for ALL stores.\n";
