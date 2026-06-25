<div class="container mt-4" style="background-color: #555555; border: 0cap;">

<hr>
<h2 style="text-align: center; font-size: 75px; font-weight: lighter ; text-decoration: underline;"><?php echo html_escape($post['title']); ?></h2>

<small class="post-date">Posted on: <?php echo html_escape($post['created_at']); ?> by: </small><br></br>
<img width="25%" height="auto" src="<?php echo site_url(); ?>assets/images/posts/<?php echo rawurlencode($post['post_image']); ?>">
<div class="post-body">
    <?php echo nl2br(html_escape($post['body'])); ?>
</div>
<?php if($this->session->userdata('user_id') == $post['user_id'] ) : ?>
<hr>
<a class="btn btn-primary pull-left" href="<?php echo base_url(); ?>posts/edit/<?php echo rawurlencode($post['slug']); ?>">Edit</a>
<?php echo form_open('/posts/delete/' . $post['id']); ?>
<input type="submit" value="Delete" class="btn btn-danger">
</form>
<?php endif; ?>
<hr>
<h3>Add Comment</h3>
<?php echo validation_errors(); ?>
<?php echo form_open('comments/create/' . $post['id']); ?>
<div class="form-group">
    <label>Name</label>
    <input required type="text" name="name" class="form-control">
</div>
<div class="form-group">
    <label>Email</label>
    <input required type="email" name="email" class="form-control">
</div>
<div class="form-group">
    <label>Body</label>
    <textarea required name="body" class="form-control"></textarea>
</div>
<input type="hidden" name="slug" value="<?php echo html_escape($post['slug']); ?>">
<br>
<button type="submit" class="btn btn-warning">Submit</button>
</form>
</div>
