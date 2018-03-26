<?php
/**
 * Joomla! POWr Icon
*
* @version 1.0
* @author POWr.io
* @package POWr
* @subpackage powr
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgButtonPowrIcon extends JPlugin{
   public function onDisplay($name){
      $jsCode = "
        function uniqueLabel(){
                    return 'xxxxxxxx_'.replace(/[x]/g, function(c) {
                        var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
                        return v.toString(16);
                    }) + new Date().getTime();
        }
        jQuery(document).on('click','.powrBtn',function(el){
          //jQuery(document).append(powrModal);
          console.log('POWr Button Clicked');
          if( jQuery(el.target).hasClass('powrBtn') || jQuery(el.target).hasClass('powrIconText') ){
                jQuery('#powrPluginsWrapper').fadeIn();
          }
          return false;
        });
        jQuery(document).on('click','.powrPlugin',function(){
          console.log('POWr Plugin');
          jQuery('#powrPluginsWrapper').fadeIn(); //Show modal
          var slug = jQuery(this).data('slug'); //Slug for app
          var shortcode = '[powr-'+slug+' id=\"'+uniqueLabel()+'\"]';
          jInsertEditorText(shortcode,editor);
          jQuery('#powrPluginsWrapper').fadeOut();
        });
        jQuery(document).on('click','.powrClose',function(){
          console.log('POWr Close');
          jQuery('#powrPluginsWrapper').fadeOut();
        });
        button_html = \"" . str_replace("\n","","
            <span class='powrIconText'>POWr Plugin</span>
            <style>
            @-webkit-keyframes ani_glow {
            0%{ text-shadow:0 0 10px #f3e8b6; }
            50%{ text-shadow:0 0 5px #cc4f4d;}
            100%{ text-shadow:0 0 10px #f3e8b6;}
            }
            @keyframes ani_glow {
            0%{ text-shadow:0 0 10px #f3e8b6; }
            50%{ text-shadow:0 0 5px #cc4f4d; }
            100%{ text-shadow:0 0 10px #f3e8b6; }
            }
            .powrIconText{
            -webkit-animation: ani_glow 2s linear infinite;
            animation: ani_glow 2s linear infinite;
            }
                .icon-powr{
                  background-image: url(//s3-us-west-1.amazonaws.com/powr/powr_logos/16x16.gif);
            width: 16px;
            height: 16px;
            position: relative;
            background-size: contain;
            background-repeat: no-repeat;
            vertical-align: top;
            }
            #powrPluginsWrapper {
            position: fixed;
            top: 15%;
            left: 25%;
            width: 50%;
            background: white;
            z-index: 99999999;
            margin: 0 auto;
            min-height: 150px;
            max-height: 60%;
            padding: 15px;
            box-shadow: 4px 5px 20px gray;
            cursor: default;
            overflow: auto;
            -webkit-animation: none;
            animation: none;

            }
            #powrPluginsWrapper .powrHeader{
            font-size: 20px;
            margin-bottom: 10px;
            }
            #powrPluginsWrapper .powrClose{
            position:absolute;
            top: 8px;
            right: 10px;
            color: gray;
            cursor:pointer;
            }
            #powrPluginsWrapper	.powrPlugin{
            float: left;
            width: 128px;
            padding 10px;
            margin: 10px 10px 5px 10px;
            border-radius: 3px;
            text-align:center;
            cursor:pointer;
            position:relative;
            }
            #powrPluginsWrapper .category {
            float: left;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            text-align: left;
            margin-top: 10px;
            }
            #powrPluginsWrapper	.powrPluginImage{
            width: 100%
            box-shadow: 3px 3px 10px #c0c0c0;
            margin-bottom: 5px;
            }
            #powrPluginsWrapper	.powrPluginTitle{
            font-size: 12px;
            font-weight: bold;
            display: block;
            height: 20px;
            }
            #powrPluginsWrapper	.powrPluginDescription{
            position: absolute;
            top: 0;
            left: 0;
            font-size: 11px;
            font-weight: 200;
            line-height: 1.2;
            opacity: 0;
            -webkit-transition: all 0.6s;
            transition: all 0.6s;
            background: rgba(255, 255, 255, 0.85);
            padding: 5px 2px;
            height: 100%;
            width: 100%;
            white-space: normal;
            }
            #powrPluginsWrapper	.powrPluginDescription:hover{
            opacity: 1;
            }
            </style>
            <div id='powrPluginsWrapper' style='display:none;'>
                <div class='powrHeader'>Select POWr Plugin</div>
                <span class='powrClose'>X</span>
                <div class='category'>Forms & Surveys</div><div class='powrPlugin' data-slug='poll'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/poll/128x128.png'>
    <span class='powrPluginTitle'>Poll</span>
    <span class='powrPluginDescription'>Learn more about your users with a custom poll!</span>
    </div><div class='powrPlugin' data-slug='order-form'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/orderForm/128x128.png'>
    <span class='powrPluginTitle'>Order Form</span>
    <span class='powrPluginDescription'>Sell anything on any site.</span>
    </div><div class='powrPlugin' data-slug='mailing-list'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/mailingList/128x128.png'>
    <span class='powrPluginTitle'>Mailing List</span>
    <span class='powrPluginDescription'>Collect contacts for your mailing list with this custom form!</span>
    </div><div class='powrPlugin' data-slug='survey'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/survey/128x128.png'>
    <span class='powrPluginTitle'>Survey</span>
    <span class='powrPluginDescription'>Unique, beautiful, responsive surveys for your site.</span>
    </div><div class='powrPlugin' data-slug='form-builder'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/formBuilder/128x128.png'>
    <span class='powrPluginTitle'>Form Builder</span>
    <span class='powrPluginDescription'>Simple, beautiful forms in seconds!</span>
    </div><div class='powrPlugin' data-slug='contact-form'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/contactForm/128x128.png'>
    <span class='powrPluginTitle'>Contact Form</span>
    <span class='powrPluginDescription'>A custom contact form for your site.</span>
    </div><div class='category'>Galleries and Sliders</div><div class='powrPlugin' data-slug='vimeo-gallery'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/vimeoGallery/128x128.png'>
    <span class='powrPluginTitle'>Vimeo Gallery</span>
    <span class='powrPluginDescription'>Sync any Vimeo user or channel to a custom gallery!</span>
    </div><div class='powrPlugin' data-slug='media-gallery'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/mediaGallery/128x128.png'>
    <span class='powrPluginTitle'>Media Gallery</span>
    <span class='powrPluginDescription'>Showcase articles, images, videos, and more!</span>
    </div><div class='powrPlugin' data-slug='multi-slider'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/multiSlider/128x128.png'>
    <span class='powrPluginTitle'>Multi Slider</span>
    <span class='powrPluginDescription'>A custom slideshow for photos, videos, and banners!</span>
    </div><div class='powrPlugin' data-slug='event-gallery'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/eventGallery/128x128.png'>
    <span class='powrPluginTitle'>Event Gallery</span>
    <span class='powrPluginDescription'>Showcase upcoming events in a beautiful Pinterest-style gallery!</span>
    </div><div class='powrPlugin' data-slug='event-slider'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/eventSlider/128x128.png'>
    <span class='powrPluginTitle'>Event Slider</span>
    <span class='powrPluginDescription'>Showcase upcoming events with images and videos in this responsive slider!</span>
    </div><div class='powrPlugin' data-slug='youtube-gallery'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/youtubeGallery/128x128.png'>
    <span class='powrPluginTitle'>YouTube Gallery</span>
    <span class='powrPluginDescription'>Sync any YouTube user, channel, or playlist!</span>
    </div><div class='powrPlugin' data-slug='vine-feed'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/vineFeed/128x128.png'>
    <span class='powrPluginTitle'>Vine Feed</span>
    <span class='powrPluginDescription'>Automatically sync any Vine @user or #hashtag!</span>
    </div><div class='powrPlugin' data-slug='flickr-gallery'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/flickrGallery/128x128.png'>
    <span class='powrPluginTitle'>Flickr Gallery</span>
    <span class='powrPluginDescription'>Sync any Flickr accounts to a custom gallery!</span>
    </div><div class='powrPlugin' data-slug='banner-slider'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/bannerSlider/128x128.png'>
    <span class='powrPluginTitle'>Banner Slider</span>
    <span class='powrPluginDescription'>Beautiful sliding banners for images and video.</span>
    </div><div class='powrPlugin' data-slug='image-slider'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/imageSlider/128x128.png'>
    <span class='powrPluginTitle'>Image Slider</span>
    <span class='powrPluginDescription'>A beautiful, responsive slider for images and photos!</span>
    </div><div class='powrPlugin' data-slug='video-gallery'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/videoGallery/128x128.png'>
    <span class='powrPluginTitle'>Video Gallery</span>
    <span class='powrPluginDescription'>Showcase YouTube and Vimeo videos in a beautiful gallery!</span>
    </div><div class='powrPlugin' data-slug='photo-gallery'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/photoGallery/128x128.png'>
    <span class='powrPluginTitle'>Photo Gallery</span>
    <span class='powrPluginDescription'>A Pinterest-style gallery for unlimited images!</span>
    </div><div class='powrPlugin' data-slug='microblog'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/microblog/128x128.png'>
    <span class='powrPluginTitle'>Microblog</span>
    <span class='powrPluginDescription'>Showcase articles, images, videos, and more!</span>
    </div><div class='powrPlugin' data-slug='video-slider'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/videoSlider/128x128.png'>
    <span class='powrPluginTitle'>Video Slider</span>
    <span class='powrPluginDescription'>A simple slideshow for YouTube and Vimeo videos!</span>
    </div><div class='category'>Social Media</div><div class='powrPlugin' data-slug='vimeo-gallery'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/vimeoGallery/128x128.png'>
    <span class='powrPluginTitle'>Vimeo Gallery</span>
    <span class='powrPluginDescription'>Sync any Vimeo user or channel to a custom gallery!</span>
    </div><div class='powrPlugin' data-slug='pinterest-feed'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/pinterestFeed/128x128.png'>
    <span class='powrPluginTitle'>Pinterest Feed</span>
    <span class='powrPluginDescription'>Display any Pinterest board on your website!</span>
    </div><div class='powrPlugin' data-slug='instagram-feed'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/instagramFeed/128x128.png'>
    <span class='powrPluginTitle'>Instagram Feed</span>
    <span class='powrPluginDescription'>Display posts from multiple Instagram accounts in a single feed!</span>
    </div><div class='powrPlugin' data-slug='facebook-feed'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/facebookFeed/128x128.png'>
    <span class='powrPluginTitle'>Facebook Feed</span>
    <span class='powrPluginDescription'>Combine and display multiple Facebook pages in a single feed!</span>
    </div><div class='powrPlugin' data-slug='social-feed'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/socialFeed/128x128.png'>
    <span class='powrPluginTitle'>Social Feed</span>
    <span class='powrPluginDescription'>Display posts from any social media network!</span>
    </div><div class='powrPlugin' data-slug='social-media-icons'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/socialMediaIcons/128x128.png'>
    <span class='powrPluginTitle'>Social Media Icons</span>
    <span class='powrPluginDescription'>Icons for Instagram, Facebook, Twitter, LinkedIn, and a million more!</span>
    </div><div class='powrPlugin' data-slug='meerkat'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/meerkat/128x128.png'>
    <span class='powrPluginTitle'>Meerkat</span>
    <span class='powrPluginDescription'>Livestream videos to any website!</span>
    </div><div class='powrPlugin' data-slug='reviews'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/reviews/128x128.png'>
    <span class='powrPluginTitle'>Reviews</span>
    <span class='powrPluginDescription'>Collect and display visitor reviews and ratings!</span>
    </div><div class='powrPlugin' data-slug='youtube-gallery'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/youtubeGallery/128x128.png'>
    <span class='powrPluginTitle'>YouTube Gallery</span>
    <span class='powrPluginDescription'>Sync any YouTube user, channel, or playlist!</span>
    </div><div class='powrPlugin' data-slug='vine-feed'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/vineFeed/128x128.png'>
    <span class='powrPluginTitle'>Vine Feed</span>
    <span class='powrPluginDescription'>Automatically sync any Vine @user or #hashtag!</span>
    </div><div class='powrPlugin' data-slug='flickr-gallery'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/flickrGallery/128x128.png'>
    <span class='powrPluginTitle'>Flickr Gallery</span>
    <span class='powrPluginDescription'>Sync any Flickr accounts to a custom gallery!</span>
    </div><div class='powrPlugin' data-slug='comments'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/comments/128x128.png'>
    <span class='powrPluginTitle'>Comments</span>
    <span class='powrPluginDescription'>Collect and display visitor comments in a custom feed!</span>
    </div><div class='powrPlugin' data-slug='rss-feed'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/rssFeed/128x128.png'>
    <span class='powrPluginTitle'>RSS Feed</span>
    <span class='powrPluginDescription'>Combine and display content from multiple RSS feeds!</span>
    </div><div class='powrPlugin' data-slug='tumblr-feed'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/tumblrFeed/128x128.png'>
    <span class='powrPluginTitle'>Tumblr Feed</span>
    <span class='powrPluginDescription'>Display multiple tumblr accounts in a single feed!</span>
    </div><div class='powrPlugin' data-slug='twitter-feed'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/twitterFeed/128x128.png'>
    <span class='powrPluginTitle'>Twitter Feed</span>
    <span class='powrPluginDescription'>Showcase any Twitter @handle or #hashtag!</span>
    </div><div class='category'>eCommerce</div><div class='powrPlugin' data-slug='plan-comparison'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/planComparison/128x128.png'>
    <span class='powrPluginTitle'>Plan Comparison</span>
    <span class='powrPluginDescription'>Display and sell products, plans, and services in a side-by-side comparison! </span>
    </div><div class='powrPlugin' data-slug='ecommerce'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/ecommerce/128x128.png'>
    <span class='powrPluginTitle'>Ecommerce</span>
    <span class='powrPluginDescription'>The simple way to sell products, downloads, and subscriptions.</span>
    </div><div class='powrPlugin' data-slug='digital-download'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/digitalDownload/128x128.png'>
    <span class='powrPluginTitle'>Digital Download</span>
    <span class='powrPluginDescription'>Sell music, videos, ebooks, and more!</span>
    </div><div class='powrPlugin' data-slug='order-form'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/orderForm/128x128.png'>
    <span class='powrPluginTitle'>Order Form</span>
    <span class='powrPluginDescription'>Sell anything on any site.</span>
    </div><div class='powrPlugin' data-slug='paypal-button'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/paypalButton/128x128.png'>
    <span class='powrPluginTitle'>PayPal Button</span>
    <span class='powrPluginDescription'>Get paid through this custom PayPal Button!</span>
    </div><div class='powrPlugin' data-slug='price-table'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/priceTable/128x128.png'>
    <span class='powrPluginTitle'>Price Table</span>
    <span class='powrPluginDescription'>Showcase pricing for products, plans, and features side-by-side!</span>
    </div><div class='category'>POWr Tools</div><div class='powrPlugin' data-slug='popup'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/popup/128x128.png'>
    <span class='powrPluginTitle'>Popup</span>
    <span class='powrPluginDescription'>Add a custom popup to any page.</span>
    </div><div class='powrPlugin' data-slug='file-embed'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/fileEmbed/128x128.png'>
    <span class='powrPluginTitle'>File Embed</span>
    <span class='powrPluginDescription'>Embed almost any file, right in your web page!</span>
    </div><div class='powrPlugin' data-slug='count-up-timer'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/countUpTimer/128x128.png'>
    <span class='powrPluginTitle'>Count Up Timer</span>
    <span class='powrPluginDescription'>Count up from any time, date, or number.</span>
    </div><div class='powrPlugin' data-slug='graph'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/graph/128x128.png'>
    <span class='powrPluginTitle'>Graph</span>
    <span class='powrPluginDescription'>Graphs and charts for any website!</span>
    </div><div class='powrPlugin' data-slug='button'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/button/128x128.png'>
    <span class='powrPluginTitle'>Button</span>
    <span class='powrPluginDescription'>Custom buttons for links, downloads, promotions, or social accounts.</span>
    </div><div class='powrPlugin' data-slug='hit-counter'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/hitCounter/128x128.png'>
    <span class='powrPluginTitle'>Hit Counter</span>
    <span class='powrPluginDescription'>Track and display visits for any page!</span>
    </div><div class='powrPlugin' data-slug='resume'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/resume/128x128.png'>
    <span class='powrPluginTitle'>Resume</span>
    <span class='powrPluginDescription'>Create, customize, and display your resume or CV!</span>
    </div><div class='powrPlugin' data-slug='faq'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/faq/128x128.png'>
    <span class='powrPluginTitle'>FAQ</span>
    <span class='powrPluginDescription'>Add a responsive FAQ section to any site.</span>
    </div><div class='powrPlugin' data-slug='reviews'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/reviews/128x128.png'>
    <span class='powrPluginTitle'>Reviews</span>
    <span class='powrPluginDescription'>Collect and display visitor reviews and ratings!</span>
    </div><div class='powrPlugin' data-slug='comments'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/comments/128x128.png'>
    <span class='powrPluginTitle'>Comments</span>
    <span class='powrPluginDescription'>Collect and display visitor comments in a custom feed!</span>
    </div><div class='powrPlugin' data-slug='about-us'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/aboutUs/128x128.png'>
    <span class='powrPluginTitle'>About Us</span>
    <span class='powrPluginDescription'>A professional profile section for your company or team.</span>
    </div><div class='powrPlugin' data-slug='tabs'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/tabs/128x128.png'>
    <span class='powrPluginTitle'>Tabs</span>
    <span class='powrPluginDescription'>Organize and display any content with custom navigation tabs!</span>
    </div><div class='powrPlugin' data-slug='holiday-countdown'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/holidayCountdown/128x128.png'>
    <span class='powrPluginTitle'>Holiday Countdown</span>
    <span class='powrPluginDescription'>Count down to any holiday, birthday, or special event!</span>
    </div><div class='powrPlugin' data-slug='map'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/map/128x128.png'>
    <span class='powrPluginTitle'>Map</span>
    <span class='powrPluginDescription'>Interactive, customizable maps for any location!</span>
    </div><div class='powrPlugin' data-slug='countdown-timer'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/countdownTimer/128x128.png'>
    <span class='powrPluginTitle'>Countdown Timer</span>
    <span class='powrPluginDescription'>Count down to any time and date.</span>
    </div><div class='powrPlugin' data-slug='weather'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/weather/128x128.png'>
    <span class='powrPluginTitle'>Weather</span>
    <span class='powrPluginDescription'>Track and display a real-time forecast for any location!</span>
    </div><div class='powrPlugin' data-slug='music-player'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/musicPlayer/128x128.png'>
    <span class='powrPluginTitle'>Music Player</span>
    <span class='powrPluginDescription'>short_description_musicplayer</span>
    </div><div class='category'>Image Manipulation</div><div class='powrPlugin' data-slug='photo-filter'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/photoFilter/128x128.png'>
    <span class='powrPluginTitle'>Photo Filter</span>
    <span class='powrPluginDescription'>Photoshop-style filters for any image!</span>
    </div><div class='powrPlugin' data-slug='photo-editor'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/photoEditor/128x128.png'>
    <span class='powrPluginTitle'>Photo Editor</span>
    <span class='powrPluginDescription'>Edit, filter, and resize any image!</span>
    </div><div class='powrPlugin' data-slug='photo-watermark'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/photoWatermark/128x128.png'>
    <span class='powrPluginTitle'>Photo Watermark</span>
    <span class='powrPluginDescription'>Add a watermark to a single image.</span>
    </div><div class='powrPlugin' data-slug='image-resizer'>
    <img class='powrPluginImage' src='//s3-us-west-1.amazonaws.com/powr/plugins/imageResizer/128x128.png'>
    <span class='powrPluginTitle'>Image Resizer</span>
    <span class='powrPluginDescription'>Adjust the size, shape, and filter for a single image.</span>
    </div>
            </div>")."\";

function loadPowrButton(){
  if( jQuery('.icon-powr').length==0){
    setTimeout(function(){
      loadPowrButton();
    },250)
    return;
  }
  jQuery('.icon-powr').parents('button').toggleClass('btn powrBtn',true);
  if( jQuery('.icon-powr').siblings('span').length > 0){ //Tinymce
    jQuery('.icon-powr').siblings('span').html( jQuery.parseHTML(button_html) );
  }else{
    jQuery('.powrBtn').append( jQuery.parseHTML(button_html) );
  }
}
loadPowrButton();
";
      $doc = JFactory::getDocument();
      $doc->addScriptDeclaration($jsCode);
          $button = new JObject();
      $button->modal = false;
      $button->class = 'btn powrBtn';
      $button->title = 'POWr Button';
      $button->text = 'POWr Plugins';
      $button->name = 'powr'; //Makes an icon-powr class
      $button->link = '#';
      return $button;
    }
}
?>