<?php
/**
 * Lớp Model cơ sở (models/BaseModel.php)
 * Kế thừa trực tiếp từ core/Model.php để đảm bảo tính tương thích kép
 */

require_once __DIR__ . '/../core/Model.php';

abstract class BaseModel extends Model {
}
