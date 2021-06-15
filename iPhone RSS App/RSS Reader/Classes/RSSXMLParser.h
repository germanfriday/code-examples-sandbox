//
//  RSSXMLParser.h
//  RSS Reader
//
//  Created by Ben on 17/08/2009.
//  Copyright 2009 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>
@class NewsItem;

@interface RSSXMLParser : NSObject {
	NSXMLParser *theXMLParser;
	NSMutableString * theCurrentString;
	NewsItem * currentNewsItem;
	NSMutableArray * newsArray;
}

@property (retain) NSMutableArray * newsArray;

-(void) parseRSSFeed;

@end
