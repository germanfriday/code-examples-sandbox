import sys

try:
    from twisted.python import dist
except ImportError:
    raise SystemExit("twisted.python.dist module not found.  Make sure you "
                     "have installed the Twisted core package before "
                     "attempting to install any other Twisted projects.")

if __name__ == '__main__':
    dist.setup(
        twisted_subproject="web",
        scripts=dist.getScripts("web"),
        # metadata
        name="Twisted Web",
        version="SVN-Trunk",
        description="Twisted web server, programmable in Python.",
        author="Twisted Matrix Laboratories",
        author_email="twisted-python@twistedmatrix.com",
        maintainer="James Knight",
        maintainer_email="foom@fuhm.net",
        url="http://twistedmatrix.com/projects/web/",
        license="MIT",
        long_description="""\
Twisted Web is a complete web server, aimed at hosting web
applications using Twisted and Python, but fully able to serve static
pages, also.
""",
        )
