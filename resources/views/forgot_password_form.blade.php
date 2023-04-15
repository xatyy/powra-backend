<!DOCTYPE html>
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html lang="ro-RO" class="no-js">
   <!--<![endif]-->
   <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
      <meta name="theme-color" content="#f55e5a"/>
      <link rel="profile" href="http://gmpg.org/xfn/11" />
      <!-- This site is optimized with the Yoast SEO plugin v16.0.1 - https://yoast.com/wordpress/plugins/seo/ -->
      <title>Reseteaza parola - Deltatel Group</title>
      <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
      <link rel="canonical" href="https://deltatelgroup.com/ro/contact/" />
      <meta property="og:locale" content="ro_RO" />
      <meta property="og:type" content="article" />
      <meta property="og:title" content="Contact - Deltatel Group" />
      <meta property="og:url" content="https://deltatelgroup.com/ro/contact/" />
      <meta property="og:site_name" content="Deltatel Group" />
      <meta property="article:modified_time" content="2021-04-19T12:56:39+00:00" />
      <meta property="og:image" content="https://deltatelgroup.com/wp-content/uploads/2020/11/logo-vectorial-1.png" />
      <meta property="og:image:width" content="2523" />
      <meta property="og:image:height" content="476" />
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:label1" content="Timp estimat pentru citire">
      <meta name="twitter:data1" content="2 minute">
     <meta name="csrf-token" content="{{ csrf_token() }}">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
     <script>
      $(document).ready(function() {
        $(".btn-reset-password").click(function() {
          $('.btn-reset-password').html('Asteptati...');
          event.preventDefault();
            $(this).attr('disabled', 'disabled');
            $.ajax({
                method: 'POST',
                url: "/api/user/reset-pass",
                data: $(this).parent().serializeArray(),
                context: this, async: true, cache: false, dataType: 'json'
            }).done(function(res) {
                setTimeout(function(){
                    $('.btn-reset-password').html("Reseteaza");
                }, 200);
                if (res.success == true) {
                    toastr.success(res.msg, 'Success!');
                    $(this).parent().trigger("reset");
                } else { 
                  if(res.msg){
                    toastr.error(res.msg, 'Error!');
                  }
                  $(".btn-reset-password").prop('disabled', false);
                }
            })
            .fail(function(xhr, status, error) {
              if(xhr && xhr.responseJSON && xhr.responseJSON.message && xhr.responseJSON.message.indexOf("CSRF token mismatch") >= 0){
                window.location.reload();
              }
            });
            return;
        });

      });
    </script>
      <link rel="icon" href="https://deltatelgroup.com/wp-content/uploads/2020/11/cropped-logo-small-1-32x32.png" sizes="32x32" />
      <link rel="icon" href="https://deltatelgroup.com/wp-content/uploads/2020/11/cropped-logo-small-1-192x192.png" sizes="192x192" />
      <link rel="apple-touch-icon" href="https://deltatelgroup.com/wp-content/uploads/2020/11/cropped-logo-small-1-180x180.png" />
      <meta name="msapplication-TileImage" content="https://deltatelgroup.com/wp-content/uploads/2020/11/cropped-logo-small-1-270x270.png" />
      <style type="text/css" data-type="vc_shortcodes-custom-css">.vc_custom_1604664424038{margin-top: 70px !important;margin-bottom: 30px !important;}.vc_custom_1604664398745{margin-top: 30px !important;}.vc_custom_1498821716836{padding-bottom: 40px !important;}.vc_custom_1498572768127{padding-bottom: 50px !important;}.vc_custom_1604664162803{padding-right: 15px !important;padding-bottom: 10px !important;padding-left: 15px !important;}.vc_custom_1475919464233{margin-top: 0px !important;margin-right: 0px !important;margin-bottom: 0px !important;margin-left: 0px !important;padding-top: 0px !important;padding-right: 10px !important;padding-bottom: 20px !important;padding-left: 10px !important;}.vc_custom_1475919323336{margin-top: 0px !important;margin-right: 0px !important;margin-bottom: 0px !important;margin-left: 0px !important;padding-top: 0px !important;padding-right: 10px !important;padding-bottom: 20px !important;padding-left: 10px !important;}.vc_custom_1605698897496{margin-bottom: 0px !important;}.vc_custom_1604665436625{margin-top: 15px !important;margin-bottom: 0px !important;}.vc_custom_1605698954972{margin-bottom: 0px !important;}.vc_custom_1604664701683{margin-top: 15px !important;margin-bottom: 0px !important;}.vc_custom_1604667858302{margin-left: 10px !important;}.vc_custom_1604665321634{margin-top: 15px !important;margin-bottom: 0px !important;}.vc_custom_1605597788484{margin-top: 15px !important;margin-bottom: 0px !important;}.vc_custom_1604665659519{margin-top: 15px !important;margin-bottom: 0px !important;}.vc_custom_1618836604413{margin-top: 15px !important;margin-bottom: 0px !important;}.vc_custom_1618836620340{margin-top: 15px !important;margin-bottom: 0px !important;}</style>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" />
     <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.js.map"></script>
     <style id='the7-custom-inline-css' type='text/css'>
         /* Column inner */
         .vc_custom_1611755794370 .wpb_wrapper .vc_column-inner{
         border-width:1px;
         border-style:solid;
         border-color:rgba(0,0,0,0.15);
         margin-top:0px;
         padding-top:30px;
         padding-left:30px;
         padding-right:30px;
         padding-bottom:30px;
         margin-left:0px;
         }
         #extra .vc_column-inner{
         border-style:none;
         }
         /* Column inner */
         .vc_custom_1611755794370 .wpb_wrapper .vc_column-inner{
         padding-left:30px;
         margin-left:55px;
         }
         /* Column inner */
         .vc_custom_1611755794370 .wpb_wrapper .vc_column-inner{
         padding-left:50px;
         padding-top:50px;
         padding-bottom:50px;
         margin-right:100px;
         }
         /* Column inner */
         #page #main .wf-wrap .wf-container-main #content .vc_custom_1611755794370 .vc_column_container .vc_column-inner .wpb_wrapper .vc_inner .vc_column_container .vc_column-inner{
         width:128% !important;
         }
         #stanga .vc_column-inner{
         height:550px;
         }
        .input-form{
          display: inline-block;
          width: 100%;
          max-width: 500px;
          height: 40px;
          padding: 9px 10px;
          font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
          font-size: 16px;
          font-weight: normal;
          line-height: 22px;
          color: #33475b;
          border: 1px solid #cbd6e2;
          box-sizing: border-box;
          -webkit-border-radius: 3px;
          -moz-border-radius: 3px;
          -ms-border-radius: 3px;
          border-radius: 3px;
          border-color: #c87872;
          margin-bottom: 10px;
        }
        .input-form::placeholder{
          color: #33475b;
        }
        .btn-reset-password {
            background: #ff7a59;
            border-color: #ff7a59;
            color: #ffffff;
            font-size: 14px;
            line-height: 14px;
            font-family: arial, helvetica, sans-serif;
            width: 100%;
            padding: 10px;
        }
        .formular-reset-password{
          width: 320px;
          margin: 0 auto;
        }
        .container-top{
          width: 100%;
          position: relative;
          padding: 34px;
          display: block;
        }
        .container-top>a{
          display: block;
          width: 320px;
          margin-left: 50px;
        }
      </style>
   </head>
   <body class="page-template-default page page-id-3635 wp-embed-responsive the7-core-ver-2.5.6 non-logged-in dt-responsive-on right-mobile-menu-close-icon ouside-menu-close-icon mobile-hamburger-close-bg-enable mobile-hamburger-close-bg-hover-enable  fade-medium-mobile-menu-close-icon fade-medium-menu-close-icon srcset-enabled btn-flat custom-btn-color custom-btn-hover-color phantom-fade phantom-shadow-decoration phantom-logo-off sticky-mobile-header top-header first-switch-logo-left first-switch-menu-right second-switch-logo-left second-switch-menu-right right-mobile-menu layzr-loading-on popup-message-style dt-fa-compatibility the7-ver-9.9.1 wpb-js-composer js-comp-ver-6.6.0.1 vc_responsive">
     <div class="container-top"> 
      <a href="https://deltatelgroup.com/ro"><img src="https://deltatelgroup.com/wp-content/uploads/2020/11/logo-vectorial-4.png"/></a>
     </div> 
     <div id="main" class="sidebar-none sidebar-divider-off">
         <div class="main-gradient"></div>
         <div class="wf-wrap">
            <div class="wf-container-main">
               <div id="content" class="content" role="main">
                  <div class="vc_row wpb_row vc_row-fluid vc_custom_1604664424038">
                     <div class="wpb_column vc_column_container vc_col-sm-12 vc_col-lg-6 vc_col-md-5 vc_col-xs-12" style="width: 100%;">
                        <div class="vc_column-inner vc_custom_1498572768127">
                           <div class="wpb_wrapper">
                              <div id="ultimate-heading-537060895b3b8170a" class="uvc-heading ult-adjust-bottom-margin ultimate-heading-537060895b3b8170a uvc-9310 " data-hspacer="no_spacer" data-halign="left" style="text-align:center">
                                 <div class="uvc-heading-spacer no_spacer" style="top"></div>
                                 <div class="uvc-main-heading ult-responsive" data-ultimate-target=".uvc-heading.ultimate-heading-537060895b3b8170a h2" data-responsive-json-new="{&quot;font-size&quot;:&quot;desktop:26px;&quot;,&quot;line-height&quot;:&quot;desktop:32px;&quot;}">
                                    <h2 style="font-weight:bold;margin-bottom:20px;">Reseteaza parola</h2>
                                 </div>
                              </div>
                              <div class="wpb_raw_code wpb_content_element wpb_raw_html">
                                 <div class="wpb_wrapper">
                                    <form class="formular-reset-password" method="post">
                                      {{csrf_field()}}
                                      <input class="input-form" name="parola1" placeholder="Introdu parola" type="password"/>
                                      <input class="input-form" name="parola2" placeholder="Repeta parola" type="password"/>
                                      <input type="hidden" name="token" value="{{$token}}"/>
                                      <button type="button" class="btn-reset btn-reset-password">Reseteaza</button>
                                   </form>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- #content -->
            </div>
            <!-- .wf-container -->
         </div>
         <!-- .wf-wrap -->
      </div>
   </body>
</html>
