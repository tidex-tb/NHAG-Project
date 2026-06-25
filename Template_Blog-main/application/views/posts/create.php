<h2><?= $title ?></h2>
<?php echo validation_errors(); ?>
<?php echo form_open_multipart('posts/create'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <h2 class="text-center text-dark mt-5">Login Form</h2>

      <div class="card my-5 bg-dark">

        <form class="card-body p-lg-5" method="post" enctype="multipart/form-data">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

          <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Add Title">
          </div>

          <div class="form-group">
            <label for="body">Body</label>
            <textarea id="body" class="form-control" name="body" placeholder="Add Body" oninput="checkWordCount()"></textarea>
            <small id="wordCountMessage" class="text-muted">Minimum 70 words required.</small>
          </div>


          <div class="form-group">
            <label for="category">Age Categories</label>
            <select id="category" name="category_id" class="form-control">
              <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label for="image">Upload Image</label>
            <input type="file" class="form-control-file" id="image" name="userfile" multiple="">
            <img id="preview-image" src="#" alt="Preview Image" style="max-width: 100%; display: none;">
          </div>


          <button type="submit" class="btn btn-primary">Submit</button>

        </form>

      </div>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  function checkWordCount() {
    var bodyText = document.getElementById("body").value;
    var wordCount = bodyText.trim().split(/\s+/).length;
    var wordCountMessage = document.getElementById("wordCountMessage");

    if (wordCount < 70) {
      wordCountMessage.textContent = "Minimum 70 words required. Current word count: " + wordCount;
      document.getElementById("body").setCustomValidity("Minimum 250 words required.");
    } else {
      wordCountMessage.textContent = "Minimum 70 words required.";
      document.getElementById("body").setCustomValidity("");
    }
  }
</script>
