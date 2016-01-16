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
* Support for detecting which kind of operating system we are running on
  * Added `GanbaroDigital\OperatingSystem\OsType\Checks\HasEtcIssue` to check for Linux-specific distro info
  * Added `GanbaroDigital\OperatingSystem\OsType\Checks\HasEtcRedhatRelease` to check for a Redhat Linux-based distro
  * Added `GanbaroDigital\OperatingSystem\OsType\ValueBuilders\BuildTypeFromFile` as the standard interface for file-based OsType builders
  * Added `GanbaroDigital\OperatingSystem\OsType\ValueBuilders\BuildTypeFromFiles` to use all available file-based resources to determine which operating system we are using
  * Added `GanbaroDigital\OperatingSystem\OsType\ValueBuilders\BuildTypeFromEtcIssue` to use `/etc/issue` to determine which Linux distro
  * Added `GanbaroDigital\OperatingSystem\OsType\ValueBuilders\BuildTypeFromEtcRedhatRelease` to use `/etc/redhat-release` to determine which Redhat Linux-based distro we have
  * Added `GanbaroDigital\OperatingSystem\OsType\ValueBuilders\BuildTypeFromLsbRelease` to use the output of `/usr/bin/lsb_release` to determine which Linux distro we are using
  * Added `GanbaroDigital\OperatingSystem\OsType\ValueBuilders\BuildTypeFromSwVers` to use the output of `/usr/bin/sw_vers` to determine which operating system we are using
