//
//  NewsCell.h
//  RSS Reader
//
//  Created by Ben on 18/08/2009.
//  Copyright 2009 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface NewsCell : UITableViewCell {
	UILabel * titleLabel;
	UILabel	* descriptionLabel;
}

@property (retain) UILabel * titleLabel;
@property (retain) UILabel * descriptionLabel;

@end
