
const tabs = document.getElementsByClassName("tab");
const contents = document.getElementsByClassName("content");
for (const element of tabs) {
   const tabId = element.getAttribute("tab-id");
   const content = document.getElementById(tabId);
   element.addEventListener("click", () => {
      for (const t of tabs) {
         t.classList.remove("active");
      }
      for (const c of contents) {
         c.classList.remove("active");
      }
      element.classList.add("active");
      content.classList.add("active");
   });
}

// input file preview
const previewImage = (id) => {
   document.getElementById(id).src = URL.createObjectURL(event.target.files[0]);
};

$(document).ready(function () {
   $(".js-example-basic-single").select2({
      width: '100%',
   });

   // SCROLL TOP
   $(".scroll-up").fadeOut();
   $(window).scroll(function () {
      if ($(this).scrollTop() > 100) {
         $(".scroll-up").fadeIn();
      } else {
         $(".scroll-up").fadeOut();
      }
   });

   //youtube video
    let vid_id = $('input[name="youtube_video_id"]').val();
    if (vid_id){
        youtubeVideoPreview(vid_id);
    }

   $(document).on("change keyup", 'input[name="youtube_video_id"]', function () {
      let vid_id = $(this).val();
       youtubeVideoPreview(vid_id);
   });

   function youtubeVideoPreview(vid_id){
       $(".youtube").css({
           "background-image":
               "url(https://img.youtube.com/vi/" + vid_id + "/maxresdefault.jpg)",
           "background-size": "cover",
       });
   }

   $(document).on("click", ".nk-video-plain-toggle", function () {
      var vid_id = $('input[name="youtube_video_id"]').val();
      playVid(vid_id);
   });

   function playVid(video_id) {
      this.isLoadingYoutube = true;
      let youtube = document.querySelector(".youtube");
      let iframe = document.createElement("iframe");

      iframe.setAttribute("frameborder", "0");
      iframe.setAttribute("allowfullscreen", "");
      iframe.setAttribute(
         "src",
         "https://www.youtube.com/embed/" +
            video_id +
            "?rel=0&showinfo=0&autoplay=1"
      );

      this.innerHTML = "";
      youtube.appendChild(iframe);
      this.isLoadingYoutube = false;
   }

});

const toggleSideMenu = () => {
   document.getElementById("sidebar").classList.toggle("active");
   document.getElementById("content").classList.toggle("active");
};
const hideSidebar = () => {
   document.getElementById("formWrapper").classList.remove("active");
   document.getElementById("formWrapper2").classList.remove("active");
};
const toggleClass = () => {
   document.getElementById("formWrapper").classList.add("active");
};
const callSignIn = () => {
   document.getElementById("formWrapper").classList.add("active");
   document.getElementById("formWrapper2").classList.remove("active");
};
const callSignUp = () => {
   document.getElementById("formWrapper").classList.remove("active");
   document.getElementById("formWrapper2").classList.add("active");
};

const toggleContactSidebar = () => {
   document.getElementById("addContactSidebar").classList.toggle("active");
};
