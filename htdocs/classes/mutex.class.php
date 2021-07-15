<?PHP

/**
 * Handles mutual exclusions using UNIX IPC-Semaphores. Thus
 * race conditions in critical sections can be avoided.
 * It uses the semaphore php extension.
 */
class Mutex
{
    private $key;
    private $sem;

    private static function isSupported()
    {
        $os = isset($_SERVER['OS']) ? $_SERVER['OS'] : '';
        return isUnixOS() && !preg_match('/^win/i', $os) && function_exists('sem_get');
    }

    /**
     * Class constructor. Initializes the semaphore key
     * for this project based on this file's directory name
     * and registers the semaphore with a value of 1
     */
    public function __construct()
    {
        if (self::isSupported()) {
            $this->key = ftok(dirname(__FILE__), "e");
            $this->sem =  sem_get($this->key, 1);
        }
    }

    /**
     * Aquires the registered semaphores. This method must be called a critical section.
     */
    function acquire()
    {
        if (self::isSupported()) {
            sem_acquire($this->sem);
        }
    }

    /**
     * Releases the semaphore. This method must be called after a criticcal section.
     */
    function release()
    {
        if (self::isSupported()) {
            sem_release($this->sem);
        }
    }
}
