# CHANGELOG

5.0.0
-----
Support Symfony 5.2.

Fix deprecation notices




4.0.0
-----
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
- With Symfony 5 the commands must return a status code. Also, the constructor arguments to the Process class changed. Returning zero and update the creation of Process class still led to failing tests. For now this problem is not relevant. Symfony 5 would have been a nice extra. Note that I made the version constraint more avoid problems in Symfony 5 applications.