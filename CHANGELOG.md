# CHANGELOG

4.3.0
-----
* Changed Job _args_ JSON Type to TEXT type to solve MySQL 5.7 values equality.

4.2.0
-----
* Job $relatedEntities property not mapped correctly. Deprecated Connection::query method, use executeQuery.

4.1.4
-----
- Updated JobManager using Types::JSON instead of Type::JSON_ARRAY or hard coded.

4.1.3
-----
- Changed return types to EntityManagerInterface.
- Changed Job $args type to JsonType.

4.1.2
-----
- Fixed README travis link.

4.1.1
-----
- (Deprecated) EntityManager::detach is deprecated; added SYMFONY_DEPRECATIONS_HELPER to handle this deprecations messages.

4.1.0
-----
- (Deprecated) Removed commented property declaring jms_job_safe_object Doctrine Type.
- (Test) Added doctrine.dbal.override_url to _doctrine.yaml_ test file to fix deprecation.

4.0.1
------
- Update Travis build

4.0.0
-----
 - Support Symfony 4 and 5.

Fix deprecation notices

- Remove ContainerAwareCommand dependency
- Update interface and dispatch calls to event manager (deprecated interface used)
- Update references to deprecated @route annotation
-Update references to the doctrine registry (current references break symfony 4 when using up to date doctrine bundle)
- Use doctrine 2
- Remove various deprecated interfaces
- Update Twig extensions (modern classes)
- Change kernel.root_dir to kernel.project_dir
- Update tests to support PHPUnit 7 and up
- Minimal PHP version bumped to 7.3 (needed to avoid version conflicts when using up to date / maintained packages)

Remaining issues:

- The detach method (entity manager) is deprecated and no replacement is available.
