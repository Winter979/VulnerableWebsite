<?php

/*
 * @Author: Zazu
 * @Date:   2018-10-11 22:24:27
 * @Last Modified by: Zazu
 * @Last Modified time: 2019-03-28 14:12:32
*/
?>
<!-- Modal That is shown uppon an error occuring-->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal modal-dialog-centered" role="document">
    <div class="modal-content bg-dark">
      <div class="modal-body p-3">
         <div class="w-100 text-center mt-2">
            <h1>Unknown error occured</h1>
         </div>
      </div>
    </div>
  </div>
</div>

<?php if($error == true): ?>
<script type="text/javascript">
   $('#errorModal').modal('show')
</script>
<?php endif ?>