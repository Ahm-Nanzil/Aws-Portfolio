<?php
include 'header.php';
$page = pageData('home')

?>

      <div id="inner-wrap" class="wrap kt-clear">
        <div
          data-elementor-type="wp-page"
          data-elementor-id="49"
          class="elementor elementor-49"
        >
          <div
            class="elementor-element elementor-element-8768b7c e-flex e-con-boxed e-con e-parent"
            data-id="8768b7c"
            data-element_type="container"
            data-settings='{
              "background_background": "slideshow",
              "background_slideshow_gallery": [
                {"id":1203,"url":"assets/img/voip-reseller.jpg"},
                {"id":1202,"url":"assets/img/voip-cc-route-service.jpg"},
                {"id":1204,"url":"assets/img/voip-calling-card.jpg"},
                {"id":1292,"url":"assets/img/voip-sms-router-service.jpg"},
                {"id":1201,"url":"assets/img/voip-a2z-service.jpg"},
                {"id":1200,"url":"assets/img/voip-calling-card-printing-service.jpg"}
              ],
              "background_slideshow_slide_transition": "slide_right",
              "background_slideshow_ken_burns": "yes",
              "background_slideshow_slide_duration": 4000,
              "background_slideshow_loop": "yes",
              "background_slideshow_transition_duration": 500,
              "background_slideshow_ken_burns_zoom_direction": "in"
            }'
          >
            <div class="e-con-inner">
              <div
                class="elementor-element elementor-element-5b7b984 elementor-widget elementor-widget-heading"
                data-id="5b7b984"
                data-element_type="widget"
                data-widget_type="heading.default"
              >
                <div class="elementor-widget-container">
                  <h2 class="elementor-heading-title elementor-size-default">
                    <?php
                      echo htmlspecialchars($page['hero']['title'] ?? 'Welcome to Our VoIP Services');
                      ?>
                  </h2>
                </div>
              </div>
            </div>
          </div>
          <div
            class="elementor-element elementor-element-7a0c3f2 e-flex e-con-boxed e-con e-parent"
            data-id="7a0c3f2"
            data-element_type="container"
            data-settings='{"background_background":"classic"}'
          >
            <div class="e-con-inner">
              <div
                class="elementor-element elementor-element-61405cd e-con-full e-flex e-con e-child"
                data-id="61405cd"
                data-element_type="container"
              >
                <div
                  class="elementor-element elementor-element-e22f0df elementor-widget elementor-widget-heading"
                  data-id="e22f0df"
                  data-element_type="widget"
                  data-widget_type="heading.default"
                >
                  <div class="elementor-widget-container">
                    <h2 class="elementor-heading-title elementor-size-default">
                      <?php
                        echo htmlspecialchars($page['voIP']['title'] ?? 'What equipment do you need to set up VoIP?');
                        ?>
                    </h2>
                  </div>
                </div>
                <div
                  class="elementor-element elementor-element-0694930 elementor-widget elementor-widget-text-editor"
                  data-id="0694930"
                  data-element_type="widget"
                  data-widget_type="text-editor.default"
                >
                  <div class="elementor-widget-container">
                    <p>
                      <?php
                        echo ($page['voIP']['description'] ?? 'For a small setup, you only need a subscription to your <a
                        href="/"
                        rel="noreferrer noopener"
                        ><strong>business VoIP provider</strong></a
                      >
                      and a device to make calls to get set up for the first
                      time. This device could be a desk phone, a softphone on
                      your laptop, or a VoIP app on your mobile phone. Assuming
                      you already have a broadband connection and a router, you
                      don’t need any other VoIP equipment. 
                      ');
                        ?>
                    </p>
                  </div>
                </div>
              </div>
              <div
                class="elementor-element elementor-element-2444b78 e-con-full e-flex e-con e-child"
                data-id="2444b78"
                data-element_type="container"
              >
                <div
                  class="elementor-element elementor-element-8e6d415 elementor-widget elementor-widget-image"
                  data-id="8e6d415"
                  data-element_type="widget"
                  data-widget_type="image.default"
                >
                  <div class="elementor-widget-container">
                    <img
                      fetchpriority="high"
                      decoding="async"
                      width="768"
                      height="384"
                      src="assets/img/benefits-of-voip.avif"
                      class="attachment-large size-large wp-image-234"
                      alt=""
                      srcset="
                        assets/img/benefits-of-voip.avif         768w,
                        assets/img/benefits-of-voip-300x150.avif 300w
                      "
                      sizes="(max-width: 768px) 100vw, 768px"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            class="elementor-element elementor-element-860b11d animated-fast e-flex e-con-boxed elementor-invisible e-con e-parent"
            data-id="860b11d"
            data-element_type="container"
            data-settings='{"background_background":"classic","animation":"fadeInUp"}'
          >
            <div class="e-con-inner">
              <div
                class="elementor-element elementor-element-33c3547 elementor-widget elementor-widget-heading"
                data-id="33c3547"
                data-element_type="widget"
                data-widget_type="heading.default"
              >
                <div class="elementor-widget-container">
                  <h2 class="elementor-heading-title elementor-size-default">
                    
                                          <?php
                      echo htmlspecialchars($page['services']['title'] ?? 'Our Valuable Services');
                      ?>
                  </h2>
                </div>
              </div>
              <div
                class="elementor-element elementor-element-2f9a5b3 elementor-widget elementor-widget-text-editor"
                data-id="2f9a5b3"
                data-element_type="widget"
                data-widget_type="text-editor.default"
              >
                <div class="elementor-widget-container">
                  <p>
                    
                    <?php
                      echo htmlspecialchars($page['services']['description'] ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut
                    elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus
                    leo. Lorem ipsum dolor sit amet, consectetur adipiscing
                    elit. Ut elit tellus, luctus nec ullamcorper mattis,
                    pulvinar dapibus leo.');
                      ?>
                  </p>
                </div>
              </div>
              <div
                class="elementor-element elementor-element-c52127b e-con-full e-flex e-con e-child"
                data-id="c52127b"
                data-element_type="container"
              >
                <div
                  class="elementor-element elementor-element-65d8751 e-con-full e-flex e-con e-child"
                  data-id="65d8751"
                  data-element_type="container"
                >
                  <div
                    class="elementor-element elementor-element-be4ae43 elementor-widget elementor-widget-image"
                    data-id="be4ae43"
                    data-element_type="widget"
                    data-widget_type="image.default"
                  >
                    <div class="elementor-widget-container">
                      <img
                        decoding="async"
                        width="1280"
                        height="852"
                        src="assets/img/voip-reseller-1.jpg"
                        class="elementor-animation-float attachment-full size-full wp-image-1335"
                        alt=""
                        srcset="
                          assets/img/voip-reseller-1.jpg          1280w,
                          assets/img/voip-reseller-1-300x200.jpg   300w,
                          assets/img/voip-reseller-1-1024x682.jpg 1024w,
                          assets/img/voip-reseller-1-768x511.jpg   768w
                        "
                        sizes="(max-width: 1280px) 100vw, 1280px"
                      />
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-5323cb1 elementor-widget elementor-widget-heading"
                    data-id="5323cb1"
                    data-element_type="widget"
                    data-widget_type="heading.default"
                  >
                    <div class="elementor-widget-container">
                      <h2
                        class="elementor-heading-title elementor-size-default"
                      >
                        <a href="<?php echo $base_url; ?>/reseller.php"
                          >
                          
                          <?php
                            echo htmlspecialchars($page['services']['card1_title'] ?? 'Reseller');
                            ?>
                          
                          </a
                        >
                      </h2>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-863992d elementor-widget elementor-widget-text-editor"
                    data-id="863992d"
                    data-element_type="widget"
                    data-widget_type="text-editor.default"
                  >
                    <div class="elementor-widget-container">
                      <p>
                        <?php
                          echo htmlspecialchars($page['services']['card1_description'] ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        Ut elit tellus, luctus nec ullamcorper mattis, pulvinar
                        dapibus leo.');
                          ?>
                        
                      </p>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-6028e76 elementor-widget elementor-widget-button"
                    data-id="6028e76"
                    data-element_type="widget"
                    data-widget_type="button.default"
                  >
                    <div class="elementor-widget-container">
                      <div class="elementor-button-wrapper">
                        <a
                          class="elementor-button elementor-button-link elementor-size-sm"
                          href="<?php echo $base_url; ?>/reseller.php"
                        >
                          <span class="elementor-button-content-wrapper">
                            <span class="elementor-button-icon">
                              <svg
                                aria-hidden="true"
                                class="e-font-icon-svg e-fas-long-arrow-alt-right"
                                viewBox="0 0 448 512"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"
                                ></path>
                              </svg>
                            </span>
                            <span class="elementor-button-text">Read More</span>
                          </span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  class="elementor-element elementor-element-e2c32e0 e-con-full e-flex e-con e-child"
                  data-id="e2c32e0"
                  data-element_type="container"
                >
                  <div
                    class="elementor-element elementor-element-f1e39ae elementor-widget elementor-widget-image"
                    data-id="f1e39ae"
                    data-element_type="widget"
                    data-widget_type="image.default"
                  >
                    <div class="elementor-widget-container">
                      <img
                        decoding="async"
                        width="1279"
                        height="853"
                        src="assets/img/voip-calling-card-printing.jpg"
                        class="elementor-animation-float attachment-full size-full wp-image-1333"
                        alt=""
                        srcset="
                          assets/img/voip-calling-card-printing.jpg          1279w,
                          assets/img/voip-calling-card-printing-300x200.jpg   300w,
                          assets/img/voip-calling-card-printing-1024x683.jpg 1024w,
                          assets/img/voip-calling-card-printing-768x512.jpg   768w
                        "
                        sizes="(max-width: 1279px) 100vw, 1279px"
                      />
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-5868e49 elementor-widget elementor-widget-heading"
                    data-id="5868e49"
                    data-element_type="widget"
                    data-widget_type="heading.default"
                  >
                    <div class="elementor-widget-container">
                      <h2
                        class="elementor-heading-title elementor-size-default"
                      >
                        <a href="<?php echo $base_url; ?>/calling-card.php"
                          >
                          <?php
                            echo htmlspecialchars($page['services']['card2_title'] ?? 'Calling Card');
                            ?>
                          </a
                        >
                      </h2>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-5e49d5e elementor-widget elementor-widget-text-editor"
                    data-id="5e49d5e"
                    data-element_type="widget"
                    data-widget_type="text-editor.default"
                  >
                    <div class="elementor-widget-container">
                      <p>
                        <?php
                            echo htmlspecialchars($page['services']['card2_description'] ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        Ut elit tellus, luctus nec ullamcorper mattis, pulvinar
                        dapibus leo.');
                            ?>
                       
                      </p>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-e8d2f1d elementor-widget elementor-widget-button"
                    data-id="e8d2f1d"
                    data-element_type="widget"
                    data-widget_type="button.default"
                  >
                    <div class="elementor-widget-container">
                      <div class="elementor-button-wrapper">
                        <a
                          class="elementor-button elementor-button-link elementor-size-sm"
                          href="<?php echo $base_url; ?>/calling-card.php"
                        >
                          <span class="elementor-button-content-wrapper">
                            <span class="elementor-button-icon">
                              <svg
                                aria-hidden="true"
                                class="e-font-icon-svg e-fas-long-arrow-alt-right"
                                viewBox="0 0 448 512"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"
                                ></path>
                              </svg>
                            </span>
                            <span class="elementor-button-text">Read More</span>
                          </span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  class="elementor-element elementor-element-c3248ce e-con-full e-flex e-con e-child"
                  data-id="c3248ce"
                  data-element_type="container"
                >
                  <div
                    class="elementor-element elementor-element-31e3363 elementor-widget elementor-widget-image"
                    data-id="31e3363"
                    data-element_type="widget"
                    data-widget_type="image.default"
                  >
                    <div class="elementor-widget-container">
                      <img
                        loading="lazy"
                        decoding="async"
                        width="1280"
                        height="853"
                        src="assets/img/banners.jpg"
                        class="elementor-animation-float attachment-full size-full wp-image-36"
                        alt=""
                        srcset="
                          assets/img/banners.jpg          1280w,
                          assets/img/banners-300x200.jpg   300w,
                          assets/img/banners-1024x682.jpg 1024w,
                          assets/img/banners-768x512.jpg   768w
                        "
                        sizes="(max-width: 1280px) 100vw, 1280px"
                      />
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-e386242 elementor-widget elementor-widget-heading"
                    data-id="e386242"
                    data-element_type="widget"
                    data-widget_type="heading.default"
                  >
                    <div class="elementor-widget-container">
                      <h2
                        class="elementor-heading-title elementor-size-default"
                      >
                        <a href="<?php echo $base_url; ?>/calling-card-print.php"
                          >
                          
                          <?php
                            echo htmlspecialchars($page['services']['card3_title'] ?? 'Calling Card Print');
                            ?>
                          
                          </a
                        >
                      </h2>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-2d1941f elementor-widget elementor-widget-text-editor"
                    data-id="2d1941f"
                    data-element_type="widget"
                    data-widget_type="text-editor.default"
                  >
                    <div class="elementor-widget-container">
                      <p>
                        <?php
                            echo htmlspecialchars($page['services']['card3_description'] ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        Ut elit tellus, luctus nec ullamcorper mattis, pulvinar
                        dapibus leo.');
                            ?>
                      </p>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-329b51f elementor-widget elementor-widget-button"
                    data-id="329b51f"
                    data-element_type="widget"
                    data-widget_type="button.default"
                  >
                    <div class="elementor-widget-container">
                      <div class="elementor-button-wrapper">
                        <a
                          class="elementor-button elementor-button-link elementor-size-sm"
                          href="<?php echo $base_url; ?>/calling-card-print.php"
                        >
                          <span class="elementor-button-content-wrapper">
                            <span class="elementor-button-icon">
                              <svg
                                aria-hidden="true"
                                class="e-font-icon-svg e-fas-long-arrow-alt-right"
                                viewBox="0 0 448 512"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"
                                ></path>
                              </svg>
                            </span>
                            <span class="elementor-button-text">Read More</span>
                          </span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  class="elementor-element elementor-element-4fceaea e-con-full e-flex e-con e-child"
                  data-id="4fceaea"
                  data-element_type="container"
                >
                  <div
                    class="elementor-element elementor-element-52748e5 elementor-widget elementor-widget-image"
                    data-id="52748e5"
                    data-element_type="widget"
                    data-widget_type="image.default"
                  >
                    <div class="elementor-widget-container">
                      <img
                        loading="lazy"
                        decoding="async"
                        width="1280"
                        height="853"
                        src="assets/img/a2z-voip-service.jpg"
                        class="elementor-animation-float attachment-full size-full wp-image-1332"
                        alt=""
                        srcset="
                          assets/img/a2z-voip-service.jpg          1280w,
                          assets/img/a2z-voip-service-300x200.jpg   300w,
                          assets/img/a2z-voip-service-1024x682.jpg 1024w,
                          assets/img/a2z-voip-service-768x512.jpg   768w
                        "
                        sizes="(max-width: 1280px) 100vw, 1280px"
                      />
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-63ce0cf elementor-widget elementor-widget-heading"
                    data-id="63ce0cf"
                    data-element_type="widget"
                    data-widget_type="heading.default"
                  >
                    <div class="elementor-widget-container">
                      <h2
                        class="elementor-heading-title elementor-size-default"
                      >
                        <a href="<?php echo $base_url; ?>/a2z-voip-route.php"
                          >
                          
                          <?php
                            echo htmlspecialchars($page['services']['card4_title'] ?? 'A2Z Voip Router');
                            ?>
                          
                          </a
                        >
                      </h2>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-954faf4 elementor-widget elementor-widget-text-editor"
                    data-id="954faf4"
                    data-element_type="widget"
                    data-widget_type="text-editor.default"
                  >
                    <div class="elementor-widget-container">
                      <p>
                        <?php
                            echo htmlspecialchars($page['services']['card4_description'] ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        Ut elit tellus, luctus nec ullamcorper mattis, pulvinar
                        dapibus leo.');
                            ?>
                      </p>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-8f57cef elementor-widget elementor-widget-button"
                    data-id="8f57cef"
                    data-element_type="widget"
                    data-widget_type="button.default"
                  >
                    <div class="elementor-widget-container">
                      <div class="elementor-button-wrapper">
                        <a
                          class="elementor-button elementor-button-link elementor-size-sm"
                          href="<?php echo $base_url; ?>/a2z-voip-route.php"
                        >
                          <span class="elementor-button-content-wrapper">
                            <span class="elementor-button-icon">
                              <svg
                                aria-hidden="true"
                                class="e-font-icon-svg e-fas-long-arrow-alt-right"
                                viewBox="0 0 448 512"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"
                                ></path>
                              </svg>
                            </span>
                            <span class="elementor-button-text">Read More</span>
                          </span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div
                class="elementor-element elementor-element-8db8532 e-con-full e-flex e-con e-child"
                data-id="8db8532"
                data-element_type="container"
              >
                <div
                  class="elementor-element elementor-element-b147dd6 e-con-full e-flex e-con e-child"
                  data-id="b147dd6"
                  data-element_type="container"
                ></div>
                <div
                  class="elementor-element elementor-element-bc92530 e-con-full e-flex e-con e-child"
                  data-id="bc92530"
                  data-element_type="container"
                >
                  <div
                    class="elementor-element elementor-element-5bb2e8a elementor-widget elementor-widget-image"
                    data-id="5bb2e8a"
                    data-element_type="widget"
                    data-widget_type="image.default"
                  >
                    <div class="elementor-widget-container">
                      <img
                        loading="lazy"
                        decoding="async"
                        width="1280"
                        height="853"
                        src="assets/img/cc-router.jpg"
                        class="elementor-animation-float attachment-full size-full wp-image-1334"
                        alt=""
                        srcset="
                          assets/img/cc-router.jpg          1280w,
                          assets/img/cc-router-300x200.jpg   300w,
                          assets/img/cc-router-1024x682.jpg 1024w,
                          assets/img/cc-router-768x512.jpg   768w
                        "
                        sizes="(max-width: 1280px) 100vw, 1280px"
                      />
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-a1e43c8 elementor-widget elementor-widget-heading"
                    data-id="a1e43c8"
                    data-element_type="widget"
                    data-widget_type="heading.default"
                  >
                    <div class="elementor-widget-container">
                      <h2
                        class="elementor-heading-title elementor-size-default"
                      >
                        <a href="<?php echo $base_url; ?>/cc-route.php"
                          >
                          
                          <?php
                            echo htmlspecialchars($page['services']['card5_title'] ?? 'CC Route');
                            ?>
                          
                          
                          </a
                        >
                      </h2>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-ba35328 elementor-widget elementor-widget-text-editor"
                    data-id="ba35328"
                    data-element_type="widget"
                    data-widget_type="text-editor.default"
                  >
                    <div class="elementor-widget-container">
                      <p>
                        <?php
                            echo htmlspecialchars($page['services']['card5_description'] ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        Ut elit tellus, luctus nec ullamcorper mattis, pulvinar
                        dapibus leo.');
                            ?>
                      </p>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-3cee784 elementor-widget elementor-widget-button"
                    data-id="3cee784"
                    data-element_type="widget"
                    data-widget_type="button.default"
                  >
                    <div class="elementor-widget-container">
                      <div class="elementor-button-wrapper">
                        <a
                          class="elementor-button elementor-button-link elementor-size-sm"
                          href="<?php echo $base_url; ?>/cc-route.php"
                        >
                          <span class="elementor-button-content-wrapper">
                            <span class="elementor-button-icon">
                              <svg
                                aria-hidden="true"
                                class="e-font-icon-svg e-fas-long-arrow-alt-right"
                                viewBox="0 0 448 512"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"
                                ></path>
                              </svg>
                            </span>
                            <span class="elementor-button-text">Read More</span>
                          </span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  class="elementor-element elementor-element-47260d8 e-con-full e-flex e-con e-child"
                  data-id="47260d8"
                  data-element_type="container"
                >
                  <div
                    class="elementor-element elementor-element-678f783 elementor-widget elementor-widget-image"
                    data-id="678f783"
                    data-element_type="widget"
                    data-widget_type="image.default"
                  >
                    <div class="elementor-widget-container">
                      <img
                        loading="lazy"
                        decoding="async"
                        width="1279"
                        height="854"
                        src="assets/img/sms-router-service-.jpg"
                        class="elementor-animation-float attachment-full size-full wp-image-1336"
                        alt=""
                        srcset="
                          assets/img/sms-router-service-.jpg          1279w,
                          assets/img/sms-router-service--300x200.jpg   300w,
                          assets/img/sms-router-service--1024x684.jpg 1024w,
                          assets/img/sms-router-service--768x513.jpg   768w
                        "
                        sizes="(max-width: 1279px) 100vw, 1279px"
                      />
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-8b17435 elementor-widget elementor-widget-heading"
                    data-id="8b17435"
                    data-element_type="widget"
                    data-widget_type="heading.default"
                  >
                    <div class="elementor-widget-container">
                      <h2
                        class="elementor-heading-title elementor-size-default"
                      >
                        <a href="<?php echo $base_url; ?>/sms-route.php"
                          >
                          
                          <?php
                            echo htmlspecialchars($page['services']['card6_title'] ?? 'SMS Route');
                            ?>
                          
                          </a
                        >
                      </h2>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-2b97b38 elementor-widget elementor-widget-text-editor"
                    data-id="2b97b38"
                    data-element_type="widget"
                    data-widget_type="text-editor.default"
                  >
                    <div class="elementor-widget-container">
                      <p>
                        <?php
                            echo htmlspecialchars($page['services']['card6_description'] ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        Ut elit tellus, luctus nec ullamcorper mattis, pulvinar
                        dapibus leo.');
                            ?>
                      </p>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-bc632a3 elementor-widget elementor-widget-button"
                    data-id="bc632a3"
                    data-element_type="widget"
                    data-widget_type="button.default"
                  >
                    <div class="elementor-widget-container">
                      <div class="elementor-button-wrapper">
                        <a
                          class="elementor-button elementor-button-link elementor-size-sm"
                          href="<?php echo $base_url; ?>/sms-route.php"
                        >
                          <span class="elementor-button-content-wrapper">
                            <span class="elementor-button-icon">
                              <svg
                                aria-hidden="true"
                                class="e-font-icon-svg e-fas-long-arrow-alt-right"
                                viewBox="0 0 448 512"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"
                                ></path>
                              </svg>
                            </span>
                            <span class="elementor-button-text">Read More</span>
                          </span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  class="elementor-element elementor-element-42919ba e-con-full e-flex e-con e-child"
                  data-id="42919ba"
                  data-element_type="container"
                ></div>
              </div>
            </div>
          </div>
          <div
            class="elementor-element elementor-element-0ed7eb7 e-flex e-con-boxed e-con e-parent"
            data-id="0ed7eb7"
            data-element_type="container"
            data-settings='{"background_background":"classic"}'
          >
            <div class="e-con-inner">
              <div
                class="elementor-element elementor-element-39fc7a9 e-con-full e-flex elementor-invisible e-con e-child"
                data-id="39fc7a9"
                data-element_type="container"
                data-settings='{"background_background":"classic","animation":"fadeInLeft"}'
              >
                <div
                  class="elementor-element elementor-element-13fad23 elementor-widget elementor-widget-heading"
                  data-id="13fad23"
                  data-element_type="widget"
                  data-widget_type="heading.default"
                >
                  <div class="elementor-widget-container">
                    <h2 class="elementor-heading-title elementor-size-default">
                      Get Contact Us
                    </h2>
                  </div>
                </div>
              </div>
              <div
                class="elementor-element elementor-element-3ea88c1 e-flex e-con-boxed elementor-invisible e-con e-child"
                data-id="3ea88c1"
                data-element_type="container"
                data-settings='{"animation":"fadeInLeft"}'
              >
                <div class="e-con-inner">
                  <div
                    class="elementor-element elementor-element-08a9c4e elementor-widget elementor-widget-heading"
                    data-id="08a9c4e"
                    data-element_type="widget"
                    data-widget_type="heading.default"
                  >
                    <div class="elementor-widget-container">
                      <h2
                        class="elementor-heading-title elementor-size-default"
                      >
                        +447441446646<br />
                        asiantelecomrasel@gmail.com
                      </h2>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-2411017 elementor-shape-rounded elementor-grid-0 e-grid-align-center elementor-widget elementor-widget-social-icons"
                    data-id="2411017"
                    data-element_type="widget"
                    data-widget_type="social-icons.default"
                  >
                    <div class="elementor-widget-container">
                      <div
                        class="elementor-social-icons-wrapper elementor-grid"
                      >
                        <span class="elementor-grid-item">
                          <a
                            class="elementor-icon elementor-social-icon elementor-social-icon-facebook elementor-repeater-item-97d4a42"
                            target="_blank"
                          >
                            <span class="elementor-screen-only">Facebook</span>
                            <svg
                              class="e-font-icon-svg e-fab-facebook"
                              viewBox="0 0 512 512"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path
                                d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z"
                              ></path>
                            </svg>
                          </a>
                        </span>
                        <span class="elementor-grid-item">
                          <a
                            class="elementor-icon elementor-social-icon elementor-social-icon-twitter elementor-repeater-item-031e676"
                            target="_blank"
                          >
                            <span class="elementor-screen-only">Twitter</span>
                            <svg
                              class="e-font-icon-svg e-fab-twitter"
                              viewBox="0 0 512 512"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path
                                d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"
                              ></path>
                            </svg>
                          </a>
                        </span>
                        <span class="elementor-grid-item">
                          <a
                            class="elementor-icon elementor-social-icon elementor-social-icon-youtube elementor-repeater-item-0815953"
                            target="_blank"
                          >
                            <span class="elementor-screen-only">Youtube</span>
                            <svg
                              class="e-font-icon-svg e-fab-youtube"
                              viewBox="0 0 576 512"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path
                                d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"
                              ></path>
                            </svg>
                          </a>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            class="elementor-element elementor-element-338a239 e-flex e-con-boxed e-con e-parent"
            data-id="338a239"
            data-element_type="container"
          >
            <div class="e-con-inner">
              <div
                class="elementor-element elementor-element-317a1c9 e-con-full e-flex e-con e-child"
                data-id="317a1c9"
                data-element_type="container"
              >
                <div
                  class="elementor-element elementor-element-0f602a1 elementor-widget elementor-widget-heading"
                  data-id="0f602a1"
                  data-element_type="widget"
                  data-widget_type="heading.default"
                >
                  <div class="elementor-widget-container">
                    <h2 class="elementor-heading-title elementor-size-default">
                      
                      <?php
                            echo htmlspecialchars($page['voip_calling']['title'] ?? 'What is a VOIP Calling Card?');
                            ?>
                    </h2>
                  </div>
                </div>
                <div
                  class="elementor-element elementor-element-86fba77 elementor-widget elementor-widget-text-editor"
                  data-id="86fba77"
                  data-element_type="widget"
                  data-widget_type="text-editor.default"
                >
                  <div class="elementor-widget-container">
                    <p>
                      <span style="font-weight: 400"
                        >
                        

                        <?php
                            echo ($page['voip_calling']['description'] ?? 'A VOIP (Voice over Internet Protocol) calling card is a
                        prepaid card that allows users to make international or
                        long-distance phone calls using Internet technology. By
                        using a calling card, customers can access lower rates
                        for calls without needing a traditional landline or
                        mobile network.');
                            ?>
                        
                        </span
                      >
                    </p>
                  </div>
                </div>
              </div>
              <div
                class="elementor-element elementor-element-a45c065 e-con-full e-flex e-con e-child"
                data-id="a45c065"
                data-element_type="container"
              >
                <div
                  class="elementor-element elementor-element-edae659 elementor-widget elementor-widget-image"
                  data-id="edae659"
                  data-element_type="widget"
                  data-widget_type="image.default"
                >
                  <div class="elementor-widget-container">
                    <img
                      loading="lazy"
                      decoding="async"
                      width="1200"
                      height="830"
                      src="assets/img/What-is-a-VOIP-Calling-Card-.jpg"
                      alt="What is a VOIP Calling Card?"
                      class="attachment-large size-large"
                    />

                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            class="elementor-element elementor-element-37a8eac e-flex e-con-boxed e-con e-parent"
            data-id="37a8eac"
            data-element_type="container"
            data-settings='{"background_background":"gradient"}'
          >
            <div class="e-con-inner">
              <div
                class="elementor-element elementor-element-e653f7d e-con-full e-flex elementor-invisible e-con e-child"
                data-id="e653f7d"
                data-element_type="container"
                data-settings='{"background_background":"classic","animation":"fadeInLeft"}'
              >
                <div
                  class="elementor-element elementor-element-891992b elementor-widget elementor-widget-heading"
                  data-id="891992b"
                  data-element_type="widget"
                  data-widget_type="heading.default"
                >
                  <div class="elementor-widget-container">
                    <h2 class="elementor-heading-title elementor-size-default">
                      
                      <?php
                            echo htmlspecialchars($page['ccroute']['title'] ?? 'Optimizing Communication for Businesses with VOIP
                      Solutions');
                            ?>
                    </h2>
                  </div>
                </div>
                <div
                  class="elementor-element elementor-element-083ad2c elementor-widget elementor-widget-text-editor"
                  data-id="083ad2c"
                  data-element_type="widget"
                  data-widget_type="text-editor.default"
                >
                  <div class="elementor-widget-container">
                    <p>
                      
                      <?php
                            echo ($page['ccroute']['description'] ?? '<span style="font-weight: 400"
                        >In the fast-paced world of modern business,
                        communication plays a pivotal role in maintaining strong
                        relationships, providing customer service, and driving
                        sales. As companies expand globally, ensuring reliable,
                        efficient, and cost-effective communication channels is
                        crucial. This is where the </span
                      ><b>CC Route</b
                      ><span style="font-weight: 400"> comes into play.</span>
                      <p>
                      <span style="font-weight: 400"
                        >CC Route is a highly effective route management system
                        specifically designed for </span
                      ><b>Voice over Internet Protocol (VOIP)</b
                      ><span style="font-weight: 400">
                        services. It enables businesses to streamline their
                        communication needs by optimizing the way calls are
                        routed, ensuring the best possible combination of cost
                        savings, quality, and reliability.</span
                      >
                    </p>
                    <p>
                      <span style="font-weight: 400"
                        >The &#8220;A2Z&#8221; in A2Z VOIP Route represents the
                        exhaustive nature of this service—it covers </span
                      ><b>all routes</b
                      ><span style="font-weight: 400"> from </span><b>A to Z</b
                      ><span style="font-weight: 400"
                        >, meaning it includes every possible destination
                        globally. By using a vast range of routes, A2Z VOIP
                        Route offers flexibility, scalability, and the ability
                        to choose from multiple providers and pathways to ensure
                        optimal call quality and pricing.</span
                      >
                    </p>
                      ');
                            ?>
                    </p>
                    
                  </div>
                </div>
              </div>
              <div
                class="elementor-element elementor-element-ea0186d e-con-full e-flex elementor-invisible e-con e-child"
                data-id="ea0186d"
                data-element_type="container"
                data-settings='{"animation":"fadeInRight"}'
              >
                <div
                  class="elementor-element elementor-element-eec95c1 elementor-widget elementor-widget-image"
                  data-id="eec95c1"
                  data-element_type="widget"
                  data-widget_type="image.default"
                >
                  <div class="elementor-widget-container">
                    <img
                      loading="lazy"
                      decoding="async"
                      width="2000"
                      height="2000"
                      src="assets/img/Optimizing-Communication-for-Businesses-with-VOIP-Solutions.jpg"
                      alt="Optimizing Communication for Businesses with VOIP Solutions"
                      class="attachment-large size-large"
                    />

                  </div>
                </div>
              </div>
            </div>
          </div>
          <div
            class="elementor-element elementor-element-37b6d08 e-flex e-con-boxed e-con e-parent"
            data-id="37b6d08"
            data-element_type="container"
            data-settings='{"background_background":"classic"}'
          >
            <div class="e-con-inner">
              <div
                class="elementor-element elementor-element-36d3f7f elementor-widget elementor-widget-heading"
                data-id="36d3f7f"
                data-element_type="widget"
                data-widget_type="heading.default"
              >
                <div class="elementor-widget-container">
                  <h2 class="elementor-heading-title elementor-size-default">
                    <?php
                            echo htmlspecialchars($page['ccroute_work']['title'] ?? 'How CC Route Works');
                            ?>
                  </h2>
                </div>
              </div>
              <div
                class="elementor-element elementor-element-d76f11a elementor-widget elementor-widget-text-editor"
                data-id="d76f11a"
                data-element_type="widget"
                data-widget_type="text-editor.default"
              >
                <div class="elementor-widget-container">
                  <p>
                    
                    <?php
                            echo ($page['ccroute_work']['description'] ?? '<span style="font-weight: 400"
                      >CC Route works by aggregating a network of VOIP carriers
                      and route providers into a central system. This allows it
                      to select and route calls through the best available paths
                      in real time, optimizing both quality and cost. Here’s how
                      it works:</span
                    >');
                            ?>
                  </p>
                </div>
              </div>
              <div
                class="elementor-element elementor-element-e3c69e0 e-con-full e-flex e-con e-child"
                data-id="e3c69e0"
                data-element_type="container"
              >
                <div
                  class="elementor-element elementor-element-c2e0c9e e-con-full e-flex e-con e-child"
                  data-id="c2e0c9e"
                  data-element_type="container"
                  data-settings='{"background_background":"classic"}'
                >
                  <div
                    class="elementor-element elementor-element-2acf981 elementor-widget__width-initial elementor-widget elementor-widget-image"
                    data-id="2acf981"
                    data-element_type="widget"
                    data-widget_type="image.default"
                  >
                    <div class="elementor-widget-container">
                      <img
                        loading="lazy"
                        decoding="async"
                        width="128"
                        height="128"
                        src="assets/img/review.png"
                        class="attachment-medium size-medium wp-image-678"
                        alt="Clear Branding"
                      />
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-ec53ec0 elementor-widget elementor-widget-heading"
                    data-id="ec53ec0"
                    data-element_type="widget"
                    data-widget_type="heading.default"
                  >
                    <div class="elementor-widget-container">
                      <h2
                        class="elementor-heading-title elementor-size-default"
                      >
                        
                        <?php
                          echo htmlspecialchars($page['ccroute_work']['card1_title'] ?? 'Call Routing Optimization');
                        ?>

                      </h2>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-acfee6d elementor-widget elementor-widget-text-editor"
                    data-id="acfee6d"
                    data-element_type="widget"
                    data-widget_type="text-editor.default"
                  >
                    <div class="elementor-widget-container">
                      <p>
                        
                        <?php
                          echo ($page['ccroute_work']['card1_description'] ?? '<span style="font-weight: 400"
                          >CC Route uses advanced algorithms to determine the
                          best route for each call. These decisions are based on
                          various factors, including the quality of the route,
                          call costs, and network conditions.</span
                        ><span style="font-weight: 400"><br /><br /></span>');
                        ?>

                      </p>
                      <p><span style="font-weight: 400"> </span></p>
                    </div>
                  </div>
                </div>
                <div
                  class="elementor-element elementor-element-67cda64 e-con-full e-flex e-con e-child"
                  data-id="67cda64"
                  data-element_type="container"
                  data-settings='{"background_background":"classic"}'
                >
                  <div
                    class="elementor-element elementor-element-c69dea1 elementor-widget__width-initial elementor-widget elementor-widget-image"
                    data-id="c69dea1"
                    data-element_type="widget"
                    data-widget_type="image.default"
                  >
                    <div class="elementor-widget-container">
                      <img
                        loading="lazy"
                        decoding="async"
                        width="128"
                        height="128"
                        src="assets/img/blockchain.png"
                        class="attachment-medium size-medium wp-image-680"
                        alt="Essential Information"
                      />
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-177bd16 elementor-widget elementor-widget-heading"
                    data-id="177bd16"
                    data-element_type="widget"
                    data-widget_type="heading.default"
                  >
                    <div class="elementor-widget-container">
                      <h2
                        class="elementor-heading-title elementor-size-default"
                      >
                        <?php
                          echo htmlspecialchars($page['ccroute_work']['card2_title'] ?? 'Multiple Carriers Integration');
                        ?>
                      </h2>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-26d6b09 elementor-widget elementor-widget-text-editor"
                    data-id="26d6b09"
                    data-element_type="widget"
                    data-widget_type="text-editor.default"
                  >
                    <div class="elementor-widget-container">
                      <p>
                        
                        <?php
                          echo ($page['ccroute_work']['card2_description'] ?? '<span style="font-weight: 400"
                          >CC Route integrates multiple carriers and routes,
                          offering businesses flexibility in choosing the most
                          suitable route for their needs. By leveraging multiple
                          routes, businesses can ensure that their calls are
                          connected reliably and affordably.</span
                        >');
                        ?>
                      </p>
                    </div>
                  </div>
                </div>
                <div
                  class="elementor-element elementor-element-3e6b4a3 e-con-full e-flex e-con e-child"
                  data-id="3e6b4a3"
                  data-element_type="container"
                  data-settings='{"background_background":"classic"}'
                >
                  <div
                    class="elementor-element elementor-element-80d2913 elementor-widget__width-initial elementor-widget elementor-widget-image"
                    data-id="80d2913"
                    data-element_type="widget"
                    data-widget_type="image.default"
                  >
                    <div class="elementor-widget-container">
                      <img
                        loading="lazy"
                        decoding="async"
                        width="128"
                        height="128"
                        src="assets/img/smartphone.png"
                        class="attachment-medium size-medium wp-image-677"
                        alt="Call Rates"
                      />
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-2d9c472 elementor-widget elementor-widget-heading"
                    data-id="2d9c472"
                    data-element_type="widget"
                    data-widget_type="heading.default"
                  >
                    <div class="elementor-widget-container">
                      <h2
                        class="elementor-heading-title elementor-size-default"
                      >
                        <?php
                          echo htmlspecialchars($page['ccroute_work']['card3_title'] ?? 'Real-Time Routing Decisions');
                        ?>
                      </h2>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-9ac2426 elementor-widget elementor-widget-text-editor"
                    data-id="9ac2426"
                    data-element_type="widget"
                    data-widget_type="text-editor.default"
                  >
                    <div class="elementor-widget-container">
                      <p>
                        <?php
                          echo ($page['ccroute_work']['card3_description'] ?? '<span style="font-weight: 400"
                          > Using real-time data, CC Route makes dynamic
                          decisions about which route to use based on the
                          current network conditions, including call volume,
                          network congestion, and any ongoing issues with a
                          particular provider.</span
                        >');
                        ?>
                      </p>
                    </div>
                  </div>
                </div>
                <div
                  class="elementor-element elementor-element-2cc9a73 e-con-full e-flex e-con e-child"
                  data-id="2cc9a73"
                  data-element_type="container"
                  data-settings='{"background_background":"classic"}'
                >
                  <div
                    class="elementor-element elementor-element-a456a42 elementor-widget__width-initial elementor-widget elementor-widget-image"
                    data-id="a456a42"
                    data-element_type="widget"
                    data-widget_type="image.default"
                  >
                    <div class="elementor-widget-container">
                      <img
                        loading="lazy"
                        decoding="async"
                        width="128"
                        height="128"
                        src="assets/img/speed.png"
                        class="attachment-medium size-medium wp-image-675"
                        alt="Design Appeal"
                      />
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-16fe2d3 elementor-widget elementor-widget-heading"
                    data-id="16fe2d3"
                    data-element_type="widget"
                    data-widget_type="heading.default"
                  >
                    <div class="elementor-widget-container">
                      <h2
                        class="elementor-heading-title elementor-size-default"
                      >
                        <?php
                          echo htmlspecialchars($page['ccroute_work']['card4_title'] ?? 'Quality of Service (QoS) Monitoring');
                        ?>
                      </h2>
                    </div>
                  </div>
                  <div
                    class="elementor-element elementor-element-6d9ab7b elementor-widget elementor-widget-text-editor"
                    data-id="6d9ab7b"
                    data-element_type="widget"
                    data-widget_type="text-editor.default"
                  >
                    <div class="elementor-widget-container">
                      <p>
                        <?php
                          echo ($page['ccroute_work']['card4_description'] ?? '<span style="font-weight: 400"
                          >The system continuously monitors the performance of
                          each route, ensuring that calls are routed through the
                          highest-quality paths. This guarantees clear,
                          uninterrupted calls for businesses and their
                          customers.</span
                        >');
                        ?>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- #inner-wrap -->
       <?php
      include 'footer.php';
      ?>
    