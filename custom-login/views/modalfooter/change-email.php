<div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title color-grey">Change Email</h4>
            </div>
            <form class="form-inline" id="change-email">
                <div class="modal-body">
                    <input type="hidden" id="userid" name="userid" value="<?php echo $user_id; ?>"/>
                    <div class="form-group hundredPercent">
                        <label for="email" class="hundredPercent w_7">Email Address</label>
                        <input type="email" class="form-control" value="<?php echo $current_user->user_email; ?>" required name="email" id="email" placeholder="Change Email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
