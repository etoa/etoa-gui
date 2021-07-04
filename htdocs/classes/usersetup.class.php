<?PHP

use EtoA\DefaultItem\DefaultItemRepository;

class Usersetup
	{
	/**
	* Add an item setlist to a given planet
	*/
	static function addItemSetListToPlanet($planetid,$userid,$setid)
	{
	    global $app;

		$planetid = intval($planetid);
		$userid = intval($userid);
		$setid = intval($setid);

        /** @var DefaultItemRepository $defaultItemRepository */
        $defaultItemRepository = $app[DefaultItemRepository::class];
        $defaultItems = $defaultItemRepository->getItemsGroupedByCategory($setid);

		// Add buildings
		if (isset($defaultItems['b'])) {
		    foreach ($defaultItems['b'] as $defaultItem) {
				dbquery("INSERT INTO
					buildlist
					(
						buildlist_building_id,
						buildlist_user_id,
						buildlist_entity_id,
						buildlist_current_level
					)
					VALUES
					(
						".$defaultItem->objectId.",
						".$userid.",
						".$planetid.",
						".$defaultItem->count."
					);");
			}
		}

		// Add technologies
        if (isset($defaultItems['t'])) {
		    foreach ($defaultItems['t'] as $defaultItem) {
				dbquery("INSERT INTO
					techlist
					(
						techlist_tech_id,
						techlist_user_id,
						techlist_entity_id,
						techlist_current_level
					)
					VALUES
					(
						".$defaultItem->objectId.",
						".$userid.",
						".$planetid.",
						".$defaultItem->count."
					);");
			}
		}

		// Add ships
        if (isset($defaultItems['s'])) {
            foreach ($defaultItems['s'] as $defaultItem) {
				dbquery("INSERT INTO
					shiplist
					(
						shiplist_ship_id,
						shiplist_user_id,
						shiplist_entity_id,
						shiplist_count
					)
					VALUES
					(
						".$defaultItem->objectId.",
						".$userid.",
						".$planetid.",
						".$defaultItem->count."
					);");
			}
		}

		// Add defense
        if (isset($defaultItems['d'])) {
            foreach ($defaultItems['d'] as $defaultItem) {
				dbquery("INSERT INTO
					deflist
					(
						deflist_def_id,
						deflist_user_id,
						deflist_entity_id,
						deflist_count
					)
					VALUES
					(
						".$defaultItem->id.",
						".$userid.",
						".$planetid.",
						".$defaultItem->count."
					);");
			}
		}
	}


	}

