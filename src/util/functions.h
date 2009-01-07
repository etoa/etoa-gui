#include <sstream>
#include <string>

template <class T>inline std::string toString(const T& t)
{
	std::stringstream ss;
	ss << t;
	return ss.str();
}
