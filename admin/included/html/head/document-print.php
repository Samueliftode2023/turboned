<!-- Place the first <script> tag in your HTML's <head> -->
<script src="https://cdn.tiny.cloud/1/ukwwlo7zylb92e2o5yr3ktd53r04zpfp9wwy3t3w7vhk6hzy/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<script>
  tinymce.init({
    selector: '#editor-documente',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker markdown',
    toolbar: 'undo redo | align lineheight | bold italic underline strikethrough | link table | numlist bullist indent outdent | blocks fontfamily fontsize',
    width: 730, 
    height: 500
  });
</script>
