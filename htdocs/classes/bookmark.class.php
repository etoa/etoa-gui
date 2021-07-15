<?PHP
class Bookmark
{
    public $target;

    public function __construct($id, $userId, $entityId, $comment)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->entityId = $entityId;
        $this->comment = $comment;
    }

    /**
     *
     */
    function loadTarget()
    {
        $this->target = Entity::createFactoryById($this->entityId);
    }
}
