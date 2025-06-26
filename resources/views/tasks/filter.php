<?php

/**
 * @var string[] $allStatuses
 */

use App\domain\Task\Domain\Model\Entities\Task as TaskEntity;;

$allStatuses = TaskEntity::getAllStatuses();

/** @var array<string, mixed> $data */
$data = $data ?? [];
$statuses = isset($data['search_status']) && is_array($data['search_status']) ? $data['search_status'] : [];

?>
<form method="post" action="/tasks/search">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <div class="input-group">
                            <label style="width: 100%;">
                                <?php echo t('Search by title'); ?>
                                <input type="search" name="search_title" class="form-control" placeholder="<?php echo t('Search by title'); ?>" value="<?php echo old('search_title', $data); ?>">
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label>
                            <?php echo t('Search by status'); ?>
                            <select class="select2" name="search_status[]" multiple="multiple" data-placeholder="<?php echo t('Any'); ?>" style="width: 100%;">
                                <?php foreach ($allStatuses as $status) { ?>
                                    <option value="<?php echo $status; ?>" <?php echo in_array($status, $statuses) ? 'selected' : ''; ?>>
                                        <?php echo t($status); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary"><?php echo t('Search'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>