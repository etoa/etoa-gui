SET(REPORT_SRC_FILES
	Report.cpp
	BattleReport.cpp
	ExploreReport.cpp
	MarketReport.cpp
	SpyReport.cpp
	OtherReport.cpp
)

ADD_LIBRARY(reports ${REPORT_SRC_FILES})

