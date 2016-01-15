# CHANGELOG

## develop branch

### New

* Support for representing a type of operating system
  * Added `GanbaroDigital\OperatingSystem\OsType\Values\OsType` as the interface that all operating system types must implement
  * Added `GanbaroDigital\OperatingSystem\OsType\Values\LinuxDistro` as the base class for types of Linux distribution
  * Added `GanbaroDigital\OperatingSystem\OsType\Values\CentOS` for CentOS Linux distributions
  * Added `GanbaroDigital\OperatingSystem\OsType\Values\Debian` for Debian Linux distributions
  * Added `GanbaroDigital\OperatingSystem\OsType\Values\LinuxMint` for LinuxMint distributions
  * Added `GanbaroDigital\OperatingSystem\OsType\Values\Ubuntu` for Ubuntu server & desktop distributions
  * Added `GanbaroDigital\OperatingSystem\OsType\Values\OSX` for OSX operating systems
