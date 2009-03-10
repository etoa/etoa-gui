#include <queue>

class EntityUpdateQueue : public std::queue<int>
{
	public:
		static EntityUpdateQueue& instance ()
		{
			static EntityUpdateQueue _instance;
			return _instance;
		}
	private:
		static EntityUpdateQueue* _instance;
		EntityUpdateQueue() :  std::queue<int>()
		{
			
		}
};
