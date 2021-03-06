import sys

try:
    from twisted.python import dist
except ImportError:
    raise SystemExit("twisted.python.dist module not found.  Make sure you "
                     "have installed the Twisted core package before "
                     "attempting to install any other Twisted projects.")

if __name__ == '__main__':
    dist.setup(
        twisted_subproject="pair",
        # metadata
        name="Twisted Pair",
        version="SVN-Trunk",
        description="Twisted Pair contains low-level networking support.",
        author="Twisted Matrix Laboratories",
        author_email="twisted-python@twistedmatrix.com",
        maintainer="Tommi Virtanen",
        maintainer_email="tv@twistedmatrix.com",
        url="http://twistedmatrix.com/projects/pair/",
        license="MIT",
        long_description="""
Raw network packet parsing routines, including ethernet, IP and UDP
packets, and tuntap support.
""",
        )
