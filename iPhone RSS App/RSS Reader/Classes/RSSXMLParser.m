//
//  RSSXMLParser.m
//  RSS Reader
//
//  Created by Ben on 17/08/2009.
//  Copyright 2009 __MyCompanyName__. All rights reserved.
//

#import "RSSXMLParser.h"
#import "NewsItem.h"

@implementation RSSXMLParser
@synthesize newsArray;

-(id) init {
	if (self = [super init]) {
		newsArray = [[NSMutableArray alloc] init];
	}
	return self;
}

-(void) parseRSSFeed {
	// create a URL object using the bbc RSS feed address
	NSURL * bbcTechURL = [NSURL URLWithString:@"http://newsrss.bbc.co.uk/rss/newsonline_uk_edition/technology/rss.xml"];
	
	// initialise the XML parser with the URL location
	theXMLParser = [[NSXMLParser alloc] initWithContentsOfURL:bbcTechURL];
	
	// set delegate to this object, the methods we need to process the XML are below
	[theXMLParser setDelegate:self];
	
	// start parsing
	[theXMLParser parse];
	
	// release the parser when it's not needed anymore
	[theXMLParser autorelease];
}


- (void)parser:(NSXMLParser *)parser didStartElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName attributes:(NSDictionary *)attributeDict {
	if ([elementName isEqualToString:@"item"]) {
		// allocate and initialise a new News Item
		currentNewsItem = [[NewsItem alloc] init];
	}
	else if ([elementName isEqualToString:@"title"] ||
			 [elementName isEqualToString:@"description"] ||
			 [elementName isEqualToString:@"link"] ) {
		// initialise the current string to receive text
		theCurrentString = [NSMutableString string];
	}
	else {
		theCurrentString = nil;
	}
}

- (void)parser:(NSXMLParser *)parser foundCharacters:(NSString *)string {
	if (theCurrentString != nil) {
		[theCurrentString appendString:string];
	}
}

- (void)parser:(NSXMLParser *)parser didEndElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName {
	if ([elementName isEqualToString:@"item"]) {
		// add it to our mutable array which represents our database
		[newsArray addObject:currentNewsItem];
		// we can release the old one as the mutable array will hold a reference to the old file
		[currentNewsItem release];
	}
	else if ([elementName isEqualToString:@"title"]) {
		currentNewsItem.title = [NSString stringWithString:theCurrentString];
	}
	else if ([elementName isEqualToString:@"description"]) {
		currentNewsItem.description = [NSString stringWithString:theCurrentString];
	}
	else if ([elementName isEqualToString:@"link"]) {
		currentNewsItem.link = [NSURL URLWithString:theCurrentString];
	}
	else if ([elementName isEqualToString:@"rss"]) {
		//[[NSNotificationCenter defaultCenter] postNotificationName:@"RSSLoaded" object:nil];
	}
}

-(void) dealloc {
	[super dealloc];
	[newsArray release];
}

@end
