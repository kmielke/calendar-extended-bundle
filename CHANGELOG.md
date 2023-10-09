# Changelog

## [1.9.2](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.9.1...v1.9.2) (2023-10-08)


### Bug Fixes

* don't copy news teaser if news contains any "weekly report" content element ([7ea6e56](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/7ea6e56e667d0cd370a8fdafc9374830e374bc56))

## [1.9.1](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.9.0...v1.9.1) (2023-10-08)


### Bug Fixes

* automatically create news teaser for weekly report ([8f08c00](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/8f08c008a50c55fb428d8eace638c3e51ebf08aa))

## [1.9.0](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.8.4...v1.9.0) (2023-10-08)


### Features

* add content element "weekly report" ([a471dcf](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/a471dcf6bfb88691c4fd333f8c29b9264cdc1b40))

## [1.8.4](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.8.3...v1.8.4) (2023-10-06)


### Bug Fixes

* correctly identify game via bfv id on result updates ([010bb61](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/010bb61f96a635efe7b8b6d1826f1ba83e5208b1))

## [1.8.3](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.8.2...v1.8.3) (2023-10-06)


### Bug Fixes

* change name for bfvGameAudience ([ec4b021](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/ec4b0218308fb8ea5c788987072e7b84caedaf4f))
* lineup is not mandatory for game report ([4b33e0e](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/4b33e0ea97407b18d207db9df32889a163621e59))

## [1.8.2](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.8.1...v1.8.2) (2023-10-06)


### Bug Fixes

* don't unpublish news if news already existed ([bc712ea](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/bc712ea002fe1eac8bc3e9be40b7005ecbebf269))

## [1.8.1](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.8.0...v1.8.1) (2023-10-06)


### Bug Fixes

* get team type for event subheadline from team if not present on game ([9453948](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/94539480f227d6e53fe927b277ed82b4a337ef3e))

## [1.8.0](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.7.0...v1.8.0) (2023-10-06)


### Features

* send emails also in html ([090824b](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/090824ba10007239df0df8b11b0b784be9858c51))

## [1.7.0](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.6.0...v1.7.0) (2023-10-05)


### Features

* send email on new game report ([f739382](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/f739382f0fffb8bf14c2c04a7a6be72d8e8dda01))

## [1.6.0](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.5.0...v1.6.0) (2023-10-05)


### Features

* automatically create game report template in news entry ([3d7f589](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/3d7f589c83824179dca9082d523fda5bb41adadf))

## [1.5.0](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.4.2...v1.5.0) (2023-10-05)


### Features

* use fussball.de to get game dates especially for tournament competitions ([cd9ce5d](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/cd9ce5df642c9e69771bc9219b94ee654a5f9e5f))


### Bug Fixes

* always use league information from fussball.de if present there ([1800bf6](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/1800bf6d325f2233a05510c005e7f72d25587f35))

## [1.4.2](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.4.1...v1.4.2) (2023-10-04)


### Bug Fixes

* don't use strict comparison in GetAllEvents-Hook ([968c184](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/968c1846de582588c85c653ae1481babbd5eaa0e))

## [1.4.1](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.4.0...v1.4.1) (2023-10-04)


### Bug Fixes

* add class to mod_eventlist_table template ([cf7cced](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/cf7cced0320f9b683795a01356568f62bb22ab6a))
* remove debug message ([82baf7e](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/82baf7e5d94afc2f28a4aa11c458d9a326e13ebd))

## [1.4.0](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.3.0...v1.4.0) (2023-10-04)


### Features

* mailer is now a service ([db62323](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/db62323aad2ddb760cd16823f0b7534942fa5a18))

## [1.3.0](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.2.0...v1.3.0) (2023-10-04)


### Features

* generate event and news alias only if not already present, make event prefix configurable ([6757a43](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/6757a43182addba9cb9e75e1d632d5813584d8d3))
* list all events from one season in a nice table view ([49d0532](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/49d05328e1c16591cafac18a60a165c1ba0c2fad))
* make news filterable by league ([34c570d](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/34c570d5d6222a98c9b7f0a3030a312346e52c39))

## [1.2.0](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.1.1...v1.2.0) (2023-10-02)


### Features

* insert line breaks in global operations for calendar ([b844405](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/b84440537ffb2d146f282e5cb494c649d6a8e10c))
* use person element from cgoit/contao-persons-bundle ([cf8dda7](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/cf8dda778337e2b627409cc79775e2347fcdab90))


### Bug Fixes

* add noopener to links to bfv on maintenance module ([3f03c42](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/3f03c422c15f547eb6fa5c3a631322464a49a90c))
* call second list child records callback if exists for calendar events ([04960de](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/04960de09eba79656e70d73587b4f44c491dc5fa))
* decode entities in mail subject on result email ([2e558be](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/2e558bea4f5ab5d29370870a103c7f7116f74dac))
* fix error in be_welcome.html5 ([1deafb2](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/1deafb2491ff891e11599364b3409ea769ead40d))
* minor issues ([972bc95](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/972bc95ac0f8a4916ac58facdae299c3ef2e6c35))
* open news in current window if source is default ([8b57c17](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/8b57c17f7a381b20cc48eb5138660f2e31735136))
* remove files which are now coming from contao-persons-bundle ([ad25d35](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/ad25d3517a35ea40c05ce0be334c1151fede6263))
* update controller.yml to use the correct contao controllers of the module ([2627aa7](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/2627aa7db77f4207bba6c3b5bcefaa1ea522a3f4))


### Miscellaneous Chores

* set reference to contao-persons-bundle ([c9d63c9](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/c9d63c97aeb266c2fbf7eb67e4baeaa5efd245f1))

## [1.1.1](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.1.0...v1.1.1) (2023-09-26)


### Bug Fixes

* show only last updated results for games in the past on backend welcome page ([22d8aae](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/22d8aae5029ccec003b5b551a1b12435b3bfc082))

## [1.1.0](https://github.com/cgoIT/contao-bfv-widgets-bundle/compare/v1.0.0...v1.1.0) (2023-09-26)


### Features

* add last updated results to backend welcome page ([a1cd43e](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/a1cd43e498b1b3fdbb6d33268601fce12303910c))
* **notification:** send email notification on new result ([0531a1a](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/0531a1aeb5896e2cde007fc8c807c732b4f788fc))


### Miscellaneous Chores

* fix release-please setup ([d74e76a](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/d74e76ad377842f3298c2ba3e4412f4bfc22210e))
* **release-please:** change release-please configuration ([1d2941e](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/1d2941e494b4d88d398e765db7a76acdc2d8845c))

## 1.0.0 (2023-09-26)


### Features

* add GITHUB_TOKEN for release-please ([3a5068e](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/3a5068e7560496e10a2e882497bbbfb6187c4c75))
* try first release-pr ([6a0cdea](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/6a0cdeac314160fa776a1f48050a9c5d5983914b))


### Miscellaneous Chores

* **ci:** download dev dependencies for unit tests ([3475daf](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/3475daf936b5b29738c68f637bbc4f472ddce452))
* **ci:** prevent duplicate ci runs ([473591d](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/473591dc9736e8842e8e00b32ef0f391d46186c4))
* **ci:** remove obsolete parameter for composer ([c77e40a](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/c77e40ab0a262ba5dd5d123206575f7a0e7c43b6))
* **docs:** add empty CHANGELOG.md ([5d89542](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/5d895428f6231acac3d62cec931619f9e55494ca))
* fix release-please setup ([a9e5192](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/a9e51923fc616423c81dab445cb90277d196ec8c))
* **infra:** minor adjustment for dependabot label creation on prs ([272b32e](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/272b32ecb006a43ab29c224ea06193f0f7844504))
* initial commit for release-please ([0807aba](https://github.com/cgoIT/contao-bfv-widgets-bundle/commit/0807aba3fe0d46110f979f74d86265591a66b59b))
