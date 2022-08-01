<style>
    @import url('https://fonts.googleapis.com/css2?family=Oxygen:wght@300;400;700&display=swap');

    /* get div chat */
    .direct-chat {
        /* relative */
        z-index: 9999;
        position: fixed !important;
        /* position:absolute; */
        top: 1%;
        right: 1%;
        /* margin-left:100%; */
        max-width: 400px !important;
        min-width: 400px !important;
        width: auto !important;
        /* min-height: 600px; */
        /* height from top to bottom */
        /* height: 400px !important; */
        /* height: inherit; */
        /* flex: none !important; */
        /* resize: both !important; */

        /* height: 100vh; Use vh as a fallback for browsers that do not support Custom Properties */
        /* height: calc(var(--vh, 1vh) * 100); */
    }

    .direct-chat .card-header h1.card-title {
        font-family: 'Oxygen', sans-serif !important;
        font-size: 16px !important;
        font-weight: bold !important;
        /* margin: 0 !important; */
    }

    #plus-minus {
        margin-bottom: 0;
    }

    /* .direct-chat .card-body { */
    /* display: flex !important; */
    /* flex-direction: column !important; */
    /* } */

    #getMsg {
        /* overflow-y: scroll !important; */
        /* -webkit-overflow-scrolling: touch !important; */
        /* flex: auto !important; */
        max-height: 700px !important;
        height: auto !important;
        overflow: auto !important;
        display: flex !important;
        flex-direction: column-reverse !important;
    }

    .direct-chat .card-body .direct-chat-messages .direct-chat-text {
        height: auto !important;
        white-space: normal !important;
        /* white-space: pre; */
        /* white-space: pre-wrap !important; */
        /* white-space: break-spaces; */
    }

    .direct-chat .card-body .direct-chat-messages .direct-chat-text .pesan {
        /* white-space: pre-line !important; */
        padding: 0px !important;
        margin: 0px !important;
        /* white-space: pre-wrap; */
        /* overflow-wrap: break-word !important;  */
    }

    /* .direct-chat .card-footer form {
  display: flex !important;
  justify-content: center !important;
  align-items: center !important;
  flex-direction: row !important;
} */

    .direct-chat .card-body .direct-chat-messages .direct-chat-text .timestamp {
        /* white-space: pre-line !important; */
        font-size: xx-small !important;
        font-style: italic !important;
        /* /* padding-top: 15px !important; */
        margin-top: 15px !important;
    }

    .profil-user {
        padding: 0%;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        margin-left: -10px;
        /* line border */
        border: 0.5px solid #057ff9;
        background-color: greenyellow;
        /* shadow */
        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.75);
    }
</style>
<!-- DIRECT CHAT SUCCESS -->
<div class="card card-success card-outline direct-chat direct-chat-success shadow-lg">
    <div class="card-header">
        <!-- <h1 class="card-title mr-3"><strong>DISKS - </strong></h1> -->
        <div class="card-title mr-4">
            <img src="<?= base_url('assets/images/disks-icon.png'); ?>" alt="" style="width: 25px; height: 25px;">
        </div>
        <!-- <div class="vl float-left"></div> -->
        <!-- vertical line -->
        <div class="card-title mr-5" id="getUserLogged"></div>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i id="plus-minus" class="fa fa-plus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body" id="badanPesan">
        <div class="direct-chat-messages" id="getMsg"></div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <form action="" method="post">
            <div class="input-group">
                <textarea id="message-input" type="text" name="message" placeholder="Type Message ..." class="form-control" spellcheck="false" data-ms-editor="true" autocomplete="false" autofocus></textarea>
                <span class="input-group-append">
                    <button type="submit" id="send" class="btn btn-success"><i class="fas fa-paper-plane"></i></button>
                </span>
            </div>
            <span id="msg_err"></span>
        </form>
    </div>
    <!-- /.card-footer-->
</div>
<!--/.direct-chat -->
<script>
    'use strict';
    $(document).ready(function() {
        $('.direct-chat').addClass('collapsed-card');
        //  $('#plus-minus').removeClass();
        //  $('#plus-minus').addClass('fas fa-plus');

        setInterval(function() {
            showUserLogin();
            showMsg();
        }, 3000);
        updateLastActivity();
        showUserLogin();
        showMsg();


        function showMsg() {
            $.ajax({
                type: "GET",
                url: "getMsg",
                async: true,
                dataType: "JSON",
                success: function(data) {
                    let html = "";
                    let user = <?= session()->get("id"); ?>;
                    for (let i = 0; i < data.length; i++) {
                        let kelas_satu = user == data[i].tc_user_id ? "right" : "left";
                        let kelas_dua = user == data[i].tc_user_id ? "left" : "right";
                        html += `<div class="direct-chat-msg ` + kelas_satu + `">
                                    <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-name float-` + kelas_satu + `">` + data[i].tc_fullname + `</span>
                                    </div>
                                    <img class="direct-chat-img" src="<?= base_url("data/profil/") ?>` + "/" + data[i].tc_image + `">
                                    <div class="direct-chat-text">
                                        <span class="pesan float-` + kelas_satu + `">` + data[i].tc_message + `</span>
                                        <br>
                                        <span class="timestamp ` + kelas_dua + `">
                                        ` + data[i].tc_date + `
                                        </span>
                                    </div>
                                </div>`;
                    }
                    $('#getMsg').html(html);
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }

        $('#send').on('click', function(e) {
            e.preventDefault();

            var msg = $('#message-input').val();
            // alert(msg);
            $.ajax({
                type: "POST",
                url: "chatt",
                dataType: "JSON",
                data: {
                    message: msg
                },
                success: function(data) {
                    // console.log('sent');
                    showMsg();
                    updateLastActivity();
                    $('#message-input').val('');
                },
                error: function(err) {
                    // console.log(err);
                    $('#msg_err').text(err.responseJSON.messages.message);
                    $('#msg_err').addClass('text-danger');
                }
            });
        });

        function showUserLogin() {
            $.ajax({
                type: "GET",
                url: "getUserLogged",
                async: true,
                dataType: "JSON",
                success: function(data) {
                    let html = "";
                    for (let i = 0; i < data.length; i++) {
                        let title = titleCase(data[i].fullname);
                        html += `<img src="<?= base_url("data/profil/") ?>` + "/" + data[i].user_image + `" title="` + title + `" class="img-size-50 img-circle profil-user float-right">`;
                    }
                    $('#getUserLogged').html(html);
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }

        // function update last_activity
        function updateLastActivity() {
            // get datetime
            let id = <?= session()->get('id'); ?>;
            let date = new Date();
            $.ajax({
                type: "POST",
                url: "updateLastActivity",
                dataType: "JSON",
                data: {
                    id: id,
                    date: date
                },
                success: function(data) {
                    //  console.log(data);
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }
        setInterval(function() {
            updateLastActivity();
        }, 180000);

        function titleCase(str) {
            var splitStr = str.toLowerCase().split(' ');
            for (var i = 0; i < splitStr.length; i++) {
                // You do not need to check if i is larger than splitStr length, as your for does that for you
                // Assign it back to the array
                splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);
            }
            // Directly return the joined string
            return splitStr.join(' ');
        }

        // calculate card height when keyboard appears

        $(window).on('resize', function() {
            var height = $(window).height();
            var height_chat = height - $('.card-header').height() - $('.card-footer').height() - 50;
            $('#badanPesan').css('height', height_chat);
        });

        $(window).trigger('resize');

    });
</script>