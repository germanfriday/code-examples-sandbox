//
//  NewsCell.m
//  RSS Reader
//
//  Created by Ben on 18/08/2009.
//  Copyright 2009 __MyCompanyName__. All rights reserved.
//

#import "NewsCell.h"


@implementation NewsCell
@synthesize titleLabel, descriptionLabel;

- (id)initWithFrame:(CGRect)frame reuseIdentifier:(NSString *)reuseIdentifier {
    if (self = [super initWithFrame:frame reuseIdentifier:reuseIdentifier]) {
        // Initialization code
		self.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
		
		// initialise the title Label
		titleLabel = [[UILabel alloc] initWithFrame:CGRectMake(10, 6, 290, 16)];
		titleLabel.font = [UIFont systemFontOfSize:14];
		titleLabel.backgroundColor = [UIColor clearColor];
		[self.contentView addSubview:titleLabel];
		[titleLabel release];
		
		// initialise the description label
		descriptionLabel = [[UILabel alloc] initWithFrame:CGRectMake(10, 22, 290, 48)];
		descriptionLabel.font = [UIFont systemFontOfSize:11];
		descriptionLabel.numberOfLines = 3;
		descriptionLabel.textColor = [UIColor darkGrayColor];
		descriptionLabel.backgroundColor = [UIColor clearColor];
		[self.contentView addSubview:descriptionLabel];
		[descriptionLabel release];
    }
    return self;
}


- (void)setSelected:(BOOL)selected animated:(BOOL)animated {

    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}


- (void)dealloc {
    [super dealloc];
}


@end
