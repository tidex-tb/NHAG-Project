<h2><?= $title ?></h2>
<?php echo validation_errors();?>
<?php echo form_open('posts/update'); ?>
<input type="hidden" name="id" value="<?php echo $post['id']; ?>">
<form>
  <div class="form-group">
    <label>Title</label>
    <input type="text" class="form-control" name="title" placeholder="Add Tilte" value="<?php echo $post['title'];?>">
  </div>
  <br><br>
  <div class="form-group">
    <label>Body</label>
    <textarea id="editor" class="form-control" name="body" placeholder="Add Body"><?php echo $post['body'] ; ?></textarea>
  </div>
  <br>
  <div class="form-group">
  	<label>Age Categories</label>
    <select name="category_id" class="form-control">
      <?php foreach($categories as $category): ?>
        <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
      <?php endforeach; ?>
    </select>
    <br>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>