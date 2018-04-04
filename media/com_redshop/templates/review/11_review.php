<div class="review_group">
  {product_loop_start}
  {review_loop_start}
  <div id="reviews_wrapper" class="row">
    <div id="reviews_leftside" class="col-sm-3">
      <div id="reviews_stars"><span>Overall Rating &nbsp;</span>{stars}</div>
      <div class="username">{username} <span>{fullname}</span></div>      
      <div class="review_reply">Would you recommend this products? :<span>Yes</span></div>
    </div>
    <div class="reviews_rightside col-sm-9">
      <div class="media-body">
        <div class="reviews_title">{title}</div>
        <div class="reviews_comment">{comment}</div>
        <div class="reviews_date">{review_date}</div>
      </div>
    </div>
  </div>
  {review_loop_end}
  {product_loop_end}
</div>