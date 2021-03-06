<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');

/**
 * Class to Manage PayPal Payment Subscription
 *
 * @package  RedSHOP
 * @since    2.5
 */
class RedshopControllerMedia_Detail extends RedshopControllerMedia_DetailDefault
{
	/**
	 * Save Media Detail
	 *
	 * @return  [type]  [description]
	 */
	public function save($apply = 0)
	{
		$post = JRequest::get('post');

		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		$model = $this->getModel('media_detail');

		$product_download_root = Redshop::getConfig()->get('PRODUCT_DOWNLOAD_ROOT');

		if (substr(Redshop::getConfig()->get('PRODUCT_DOWNLOAD_ROOT'), -1) != DIRECTORY_SEPARATOR)
		{
			$product_download_root = Redshop::getConfig()->get('PRODUCT_DOWNLOAD_ROOT') . '/';
		}

		$bulkfile = JRequest::getVar('bulkfile', null, 'files', 'array');
		$bulkfiletype = strtolower(JFile::getExt($bulkfile['name']));
		$file = JRequest::getVar('file', 'array', 'files', 'array');

		if ($bulkfile['name'] == null && $file['name'][0] == null && $post['oldmedia'] != "")
		{
			if ($post['media_bank_image'] == "")
			{
				$post ['media_id'] = $cid[0];
				$post['media_name'] = $post['oldmedia'];

				if ($post['media_type'] != $post['oldtype'])
				{
					$old_path = JPATH_COMPONENT_SITE . '/assets/' . $post['oldtype'] . '/' . $post['media_section'] . '/' . $post['media_name'];
					$old_thumb_path = JPATH_COMPONENT_SITE . '/assets/' . $post['oldtype']
						. '/' . $post['media_section'] . '/thumb/' . $post['media_name'];

					$new_path = JPATH_COMPONENT_SITE . '/assets/' . $post['media_type']
						. '/' . $post['media_section'] . '/' . RedShopHelperImages::cleanFileName($post['media_name']);

					copy($old_path, $new_path);

					unlink($old_path);
					unlink($old_thumb_path);
				}

				if ($save = $model->store($post))
				{
					$msg = JText::_('COM_REDSHOP_MEDIA_DETAIL_SAVED');

					// Set First Image as product Main Imaged
					if ($save->media_section == 'product' && $save->media_type == 'images')
					{
						if (isset($post['set']) && $post['media_section'] != 'manufacturer')
						{
							$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media&section_id='
								. $post['section_id'] . '&showbuttons=1&section_name='
								. $post['section_name'] . '&media_section=' . $post['media_section'], $msg
							);
						}

						elseif (isset($post['set']) && $post['media_section'] == 'manufacturer')
						{
							$link = 'index.php?option=com_redshop&view=manufacturer';        ?>
							<script language="javascript" type="text/javascript">
								window.parent.document.location = '<?php echo $link; ?>';
							</script><?php
						}
						else
						{
							$this->setRedirect('index.php?option=com_redshop&view=media', $msg);
						}
					}
					else
					{
						if (isset($post['set']) && $post['media_section'] != 'manufacturer')
						{
							$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media&section_id='
								. $post['section_id'] . '&showbuttons=1&section_name='
								. $post['section_name'] . '&media_section=' . $post['media_section'], $msg
							);
						}
						else
						{
							$this->setRedirect('index.php?option=com_redshop&view=media', $msg);
						}
					}
				}
				else
				{
					$msg = JText::_('COM_REDSHOP_ERROR_SAVING_MEDIA_DETAIL');

					if (isset($post['set']))
					{
						$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail&section_id='
							. $post['section_id'] . '&showbuttons=1&section_name='
							. $post['section_name'] . '&media_section=' . $post['media_section'], $msg, 'warning'
						);
					}
					else
					{
						$this->setRedirect('index.php?option=com_redshop&view=media_detail', $msg, 'warning');
					}
				}
			}
			else
			{
				if ($cid [0] != 0)
				{
					$model->delete($cid);
					$post['bulk'] = 'no';
					$post ['media_id'] = 0;
				}

				// Media Bank Start

				$image_split = explode('/', $post['media_bank_image']);

				// Make the filename unique
				$filename = RedShopHelperImages::cleanFileName($image_split[count($image_split) - 1]);

				// Download product changes
				if ($post['media_type'] == 'download')
				{
					$post['media_name'] = $product_download_root . str_replace(" ", "_", $filename);
					$dest = $post['media_name'];
				}
				else
				{
					$post['media_name'] = $filename;
					$dest = JPATH_COMPONENT_SITE . '/assets/' . $post['media_type'] . '/' . $post['media_section'] . '/' . $filename;
				}

				$model->store($post);

				// Image Upload
				$src = JPATH_ROOT . '/' . $post['media_bank_image'];
				copy($src, $dest);

				// 	Media Bank End
				if (isset($post['set']) && $post['media_section'] != 'manufacturer')
				{
					$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media&section_id='
						. $post['section_id'] . '&showbuttons=1&section_name='
						. $post['section_name'] . '&media_section=' . $post['media_section'], $msg
					);
				}
				elseif (isset($post['set']) && $post['media_section'] == 'manufacturer')
				{
					$link = 'index.php?option=com_redshop&view=manufacturer';        ?>
					<script language="javascript" type="text/javascript">
						window.parent.document.location = '<?php echo $link; ?>';
					</script><?php
				}
				else
				{
					$this->setRedirect('index.php?option=com_redshop&view=media', $msg);
				}
			}
		}
		else
		{
			if ($cid [0] != 0)
			{
				$model->delete($cid);
				$post['bulk'] = 'no';
			}

			// If file selected from download folder...
			if (isset($post['hdn_download_file']) && $post['hdn_download_file'] != "")
			{
				if ($post['media_type'] == 'download')
				{
					$download_path = $product_download_root . $post['hdn_download_file_path'];
					$post['media_name'] = $post['hdn_download_file'];
				}
				else
				{
					$download_path = "product" . '/' . $post['hdn_download_file'];
					$post['media_name'] = $post['hdn_download_file'];
				}

				$filenewtype = strtolower(JFile::getExt($post['hdn_download_file']));
				$post['media_mimetype'] = $filenewtype;

				if ($post['hdn_download_file_path'] != $download_path)
				{
					// Make the filename unique
					$filename = RedShopHelperImages::cleanFileName($post['hdn_download_file']);

					if ($post['media_type'] == 'download')
					{
						$post['media_name'] = $product_download_root . $filename;

						$down_src = $download_path;

						$down_dest = $post['media_name'];
					}
					else
					{
						$post['media_name'] = $filename;

						$down_src = JPATH_COMPONENT_SITE . '/assets/' . $post['media_type'] . '/' . $post['hdn_download_file_path'];

						$down_dest = JPATH_COMPONENT_SITE . '/assets/' . $post['media_type'] . '/' . $post['media_section'] . '/' . $post['media_name'];
					}

					copy($down_src, $down_dest);
				}

				if ($save = $model->store($post))
				{
					$msg = JText::_('COM_REDSHOP_MEDIA_DETAIL_SAVED');

					if (isset($post['set']) && $post['media_section'] != 'manufacturer')
					{
						$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media&section_id='
							. $post['section_id'] . '&showbuttons=1&section_name='
							. $post['section_name'] . '&media_section=' . $post['media_section'], $msg
						);
					}

					// Set First Image as product Main Imaged
					else if ($save->media_section == 'product')
					{
						$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail', $msg);
					}
				}
				else
				{
					$msg = JText::_('COM_REDSHOP_ERROR_SAVING_MEDIA_DETAIL');

					if (isset($post['set']))
					{
						$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail&section_id=' . $post['section_id'] . '&showbuttons=1&section_name='
							. $post['section_name'] . '&media_section=' . $post['media_section'], $msg, 'warning'
						);
					}
					else
					{
						$this->setRedirect('index.php?option=com_redshop&view=media_detail', $msg, 'warning');
					}
				}
			}

			// Media Bank Start
			if ($post['media_bank_image'] != "")
			{
				$image_split = explode('/', $post['media_bank_image']);

				// Make the filename unique
				$filename = RedShopHelperImages::cleanFileName($image_split[count($image_split) - 1]);

				// Download product changes
				if ($post['media_type'] == 'download')
				{
					$post['media_name'] = $product_download_root . str_replace(" ", "_", $filename);
					$dest = $post['media_name'];
				}
				else
				{
					$post['media_name'] = $filename;
					$dest = JPATH_COMPONENT_SITE . '/assets/' . $post['media_type'] . '/' . $post['media_section'] . '/' . $filename;
				}

				$model->store($post);

				// Image Upload
				$src = JPATH_ROOT . '/' . $post['media_bank_image'];
				copy($src, $dest);

				if (isset($post['set']) && $post['media_section'] != 'manufacturer' && $post['oldmedia'] == "")
				{
					$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media&section_id=' . $post['section_id'] . '&showbuttons=1&section_name='
						. $post['section_name'] . '&media_section=' . $post['media_section'], $msg
					);
				}
				elseif (isset($post['set']) && $post['media_section'] == 'manufacturer')
				{
					$link = 'index.php?option=com_redshop&view=manufacturer';        ?>
					<script language="javascript" type="text/javascript">
						window.parent.document.location = '<?php echo $link; ?>';
					</script><?php
				}
				else
				{
					$this->setRedirect('index.php?option=com_redshop&view=media', $msg);
				}
			}

			// Media Bank End
			$post ['media_id'] = 0;
			$directory = self::writableCell('components/com_redshop/assets');

			if ($directory == 0)
			{
				$msg = JText::_('COM_REDSHOP_PLEASE_CHECK_DIRECTORY_PERMISSION');
				JFactory::getApplication()->enqueueMessage($msg, 'error');
			}

			// Starting of Bull upload creation
			if ($bulkfile['name'] != '')
			{
				if ($bulkfiletype == "zip" || $bulkfiletype == "gz" || $bulkfiletype == "tar" || $bulkfiletype == "tgz" || $bulkfiletype == "gzip")
				{
					// Fix the width of the thumb nail images
					$src = $bulkfile['tmp_name'];
					$dest = JPATH_ROOT . '/components/com_redshop/assets/' . $post['media_type'] . '/' . $post['media_section'] . '/'
						. $bulkfile['name'];
					$file_upload = JFile::upload($src, $dest);

					if ($file_upload != 1)
					{
						$msg = JText::_('COM_REDSHOP_PLEASE_CHECK_DIRECTORY_PERMISSION');
						JFactory::getApplication()->enqueueMessage($msg, 'error');
					}

					$target = 'components/com_redshop/assets/media/extracted/' . $bulkfile['name'];
					JArchive::extract($dest, $target);
					$name = explode('.', $bulkfile['name']);
					$scan = scandir($target);

					for ($i = 2, $in = count($scan); $i < $in; $i++)
					{
						if (is_dir($target . '/' . $scan[$i]))
						{
							$newscan = scandir($target . '/' . $scan[$i]);

							for ($j = 2, $jn = count($newscan); $j < $jn; $j++)
							{
								$filenewtype = strtolower(JFile::getExt($newscan[$j]));
								$btsrc = $target . '/' . $scan[$i] . '/' . $newscan[$j];
								$post['media_name'] = RedShopHelperImages::cleanFileName($newscan[$j]);
								$post['media_mimetype'] = $filenewtype;

								if ($post['media_type'] == 'download')
								{
									$post['media_name'] = $product_download_root . RedShopHelperImages::cleanFileName($newscan[$j]);

									if ($row = $model->store($post))
									{
										$originaldir = $post['media_name'];
										copy($btsrc, $originaldir);
										unlink($btsrc);

										$msg = JText::_('COM_REDSHOP_MEDIA_DETAIL_SAVED');

										if (isset($post['set']) && $post['media_section'] != 'manufacturer')
										{
											$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media&section_id='
												. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name']
												. '&media_section=' . $post['media_section'], $msg
											);
										}
										elseif (isset($post['set']) && $post['media_section'] == 'manufacturer')
										{
											$link = 'index.php?option=com_redshop&view=manufacturer';    ?>
											<script language="javascript" type="text/javascript">
												window.parent.document.location = '<?php echo $link; ?>';
											</script><?php
										}
										else
										{
											$this->setRedirect('index.php?option=com_redshop&view=media', $msg);
										}
									}
									else
									{
										$msg = JText::_('COM_REDSHOP_ERROR_SAVING_MEDIA_DETAIL');

										if (isset($post['set']))
										{
											$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail&section_id='
												. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name']
												. '&media_section=' . $post['media_section'], $msg, 'warning'
											);
										}
										else
										{
											$this->setRedirect('index.php?option=com_redshop&view=media_detail', $msg, 'warning');
										}
									}
								}
								else
								{
									if ($filenewtype == 'png' || $filenewtype == 'gif' || $filenewtype == 'jpg' || $filenewtype == 'jpeg')
									{
										if ($row = $model->store($post))
										{
											$originaldir = JPATH_ROOT . '/components/com_redshop/assets/' . $row->media_type . '/'
												. $row->media_section . '/' . RedShopHelperImages::cleanFileName($newscan[$j]);

											copy($btsrc, $originaldir);
											unlink($btsrc);
											$msg = JText::_('COM_REDSHOP_MEDIA_DETAIL_SAVED');

											if (isset($post['set']) && $post['media_section'] != 'manufacturer')
											{
												$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media&section_id='
													. $post['section_id'] . '&showbuttons=1&section_name='
													. $post['section_name'] . '&media_section=' . $post['media_section'], $msg
												);
											}

											elseif (isset($post['set']) && $post['media_section'] == 'manufacturer')
											{
												$link = 'index.php?option=com_redshop&view=manufacturer';    ?>
												<script language="javascript" type="text/javascript">
													window.parent.document.location = '<?php echo $link; ?>';
												</script><?php
											}
											else
											{
												$this->setRedirect('index.php?option=com_redshop&view=media', $msg);
											}
										}
									}
									else
									{
										$msg = JText::_('COM_REDSHOP_ERROR_SAVING_MEDIA_DETAIL');

										if (isset($post['set']))
										{
											$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail&section_id='
												. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name'] . '&media_section='
												. $post['media_section'], $msg, 'warning'
											);
										}
										else
										{
											$this->setRedirect('index.php?option=com_redshop&view=media_detail', $msg, 'warning');
										}
									}
								}
							}
						}
						else
						{
							$filenewtype = strtolower(JFile::getExt($scan[$i]));
							$btsrc = $target . '/' . $scan[$i];
							$post['media_name'] = RedShopHelperImages::cleanFileName($scan[$i]);
							$post['media_mimetype'] = $filenewtype;

							if ($post['media_type'] == 'download')
							{
								$post['media_name'] = $product_download_root . RedShopHelperImages::cleanFileName($scan[$i]);

								if ($row = $model->store($post))
								{
									$originaldir = $post['media_name'];
									copy($btsrc, $originaldir);
									unlink($btsrc);
									$msg = JText::_('COM_REDSHOP_MEDIA_DETAIL_SAVED');

									if (isset($post['set']) && $post['media_section'] != 'manufacturer')
									{
										$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media&section_id='
											. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name'] . '&media_section='
											. $post['media_section'], $msg
										);
									}

									elseif (isset($post['set']) && $post['media_section'] == 'manufacturer')
									{
										$link = 'index.php?option=com_redshop&view=manufacturer';    ?>
										<script language="javascript" type="text/javascript">
											window.parent.document.location = '<?php echo $link; ?>';
										</script><?php
									}
									else
									{
										$this->setRedirect('index.php?option=com_redshop&view=media', $msg);
									}
								}
								else
								{
									$msg = JText::_('COM_REDSHOP_ERROR_SAVING_MEDIA_DETAIL');

									if (isset($post['set']))
									{
										$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail&section_id='
											. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name'] . '&media_section='
											. $post['media_section'], $msg, 'warning'
										);
									}
									else
									{
										$this->setRedirect('index.php?option=com_redshop&view=media_detail', $msg, 'warning');
									}
								}
							}
							else
							{
								if ($filenewtype == 'png' || $filenewtype == 'gif' || $filenewtype == 'jpg' || $filenewtype == 'jpeg')
								{
									if ($row = $model->store($post))
									{
										// Set First Image as product Main Imaged
										$originaldir = JPATH_ROOT . '/components/com_redshop/assets/' . $row->media_type . '/'
											. $row->media_section . '/' . RedShopHelperImages::cleanFileName($scan[$i]);

										copy($btsrc, $originaldir);

										if (is_file($btsrc))
										{
											unlink($btsrc);
										}

										if (is_file($target))
										{
											rmdir($target . '/' . $name[0]);
											rmdir($target);
											unlink($dest);

											return true;
										}

										$msg = JText::_('COM_REDSHOP_MEDIA_DETAIL_SAVED');

										if (isset($post['set']) && $post['media_section'] != 'manufacturer')
										{
											$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media&section_id='
												. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name']
												. '&media_section=' . $post['media_section'], $msg
											);
										}
										elseif (isset($post['set']) && $post['media_section'] == 'manufacturer')
										{
											$link = 'index.php?option=com_redshop&view=manufacturer';    ?>
											<script language="javascript" type="text/javascript">
												window.parent.document.location = '<?php echo $link; ?>';
											</script><?php
										}
										else
										{
											$this->setRedirect('index.php?option=com_redshop&view=media', $msg);
										}
									}
								}
								else
								{
									$msg = JText::_('COM_REDSHOP_ERROR_SAVING_MEDIA_DETAIL');

									if (isset($post['set']))
									{
										$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail&section_id='
											. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name'] . '&media_section='
											. $post['media_section'], $msg, 'warning'
										);
									}
									else
									{
										$this->setRedirect('index.php?option=com_redshop&view=media_detail', $msg, 'warning');
									}
								}
							}
						}
					}
				}
				elseif ($bulkfiletype == 'png' || $bulkfiletype == 'gif' || $bulkfiletype == 'jpg' || $bulkfiletype == 'pdf'
					|| $bulkfiletype != 'mpeg' || $bulkfiletype != 'mp4' || $bulkfiletype != 'avi' || $bulkfiletype != '3gp'
					|| $bulkfiletype != 'swf' || $bulkfiletype != 'jpeg')
				{
					$msg = JText::_('COM_REDSHOP_PLEASE_SELECT_NO');

					if (isset($post['set']))
					{
						$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail&section_id='
							. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name'] . '&media_section='
							. $post['media_section'], $msg, 'warning'
						);
					}
					else
					{
						$this->setRedirect('index.php?option=com_redshop&view=media_detail', $msg, 'warning');
					}
				}
				else
				{
					$msg = JText::_('COM_REDSHOP_MEDIA_FILE_EXTENSION_WRONG');

					if (isset($post['set']))
					{
						$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail&section_id='
							. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name'] . '&media_section='
							. $post['media_section'], $msg, 'warning'
						);
					}
					else
					{
						$this->setRedirect('index.php?option=com_redshop&view=media_detail', $msg, 'warning');
					}
				}
			}

			if ($file['name'][0] != '')
			{
				$num = count($file['name']);

				for ($i = 0; $i < $num; $i++)
				{
					$filetype = strtolower(JFile::getExt($file['name'][$i]));

					if ($filetype != 'png' && $filetype != 'gif' && $filetype != 'jpeg' && $filetype != 'jpg' && $filetype != 'zip'
						&& $filetype != 'mpeg' && $filetype != 'mp4' && $filetype != 'avi' && $filetype != '3gp'
						&& $filetype != 'swf' && $filetype != 'pdf' && $post['media_type'] != 'download'
						&& $post['media_type'] != 'document')
					{
						$msg = JText::_('COM_REDSHOP_MEDIA_FILE_EXTENSION_WRONG');

						if (isset($post['set']))
						{
							$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail&section_id='
								. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name'] . '&media_section='
								. $post['media_section'], $msg, 'warning'
							);
						}
						else
						{
							$this->setRedirect('index.php?option=com_redshop&view=media_detail', $msg, 'warning');
						}
					}
					elseif ($post['media_section'] == '0')
					{
						$msg = JText::_('COM_REDSHOP_SELECT_MEDIA_SECTION_FIRST');

						if (isset($post['set']))
						{
							$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail&section_id='
								. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name'] . '&media_section='
								. $post['media_section'], $msg, 'warning'
							);
						}
						else
						{
							$this->setRedirect('index.php?option=com_redshop&view=media_detail', $msg, 'warning');
						}
					}
					elseif ($post['bulk'] != 'yes' && $post['bulk'] != 'no')
					{
						$msg = JText::_('COM_REDSHOP_PLEASE_SELECT_BULK_OPTION');

						if (isset($post['set']))
						{
							$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail&section_id='
								. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name'] . '&media_section='
								. $post['media_section'], $msg, 'warning'
							);
						}
						else
						{
							$this->setRedirect('index.php?option=com_redshop&view=media_detail', $msg, 'warning');
						}
					}

					elseif ($post['bulk'] == 'no' && $filetype == 'zip' && $post['media_type'] != 'download')
					{
						$msg = JText::_('COM_REDSHOP_YOU_HAVE_SELECTED_NO_OPTION');

						if (isset($post['set']))
						{
							$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail&section_id='
								. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name'] . '&media_section='
								. $post['media_section'], $msg, 'warning'
							);
						}
						else
						{
							$this->setRedirect('index.php?option=com_redshop&view=media_detail', $msg, 'warning');
						}
					}
					else
					{
						$src = $file['tmp_name'][$i];

						$file['name'][$i] = str_replace(" ", "_", $file['name'][$i]);

						// Download product changes
						if ($post['media_type'] == 'download')
						{
							$post['media_name'] = $product_download_root . RedShopHelperImages::cleanFileName($file['name'][$i]);
							$dest = $post['media_name'];
						}
						else
						{
							$post['media_name'] = RedShopHelperImages::cleanFileName($file['name'][$i]);
							$dest = JPATH_ROOT . '/components/com_redshop/assets/' . $post['media_type'] . '/'
								. $post['media_section'] . '/' . RedShopHelperImages::cleanFileName($file['name'][$i]);
						}

						$post['media_mimetype'] = $file['type'][$i];
						$file_upload = JFile::upload($src, $dest);

						if ($file_upload == 1 && $row = $model->store($post))
						{
							$msg = JText::_('COM_REDSHOP_MEDIA_DETAIL_SAVED');

							if (isset($post['set']) && $post['media_section'] != 'manufacturer')
							{
								$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media&section_id='
									. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name'] . '&media_section='
									. $post['media_section'], $msg
								);
							}

							elseif (isset($post['set']) && $post['media_section'] == 'manufacturer')
							{
								$link = 'index.php?option=com_redshop&view=manufacturer';    ?>
								<script language="javascript" type="text/javascript">
									window.parent.document.location = '<?php echo $link; ?>';
								</script><?php
							}
							else
							{
								$this->setRedirect('index.php?option=com_redshop&view=media', $msg);
							}
						}
						else
						{
							$msg = JText::_('COM_REDSHOP_ERROR_SAVING_MEDIA_DETAIL');

							if (isset($post['set']))
							{
								$this->setRedirect('index.php?tmpl=component&option=com_redshop&view=media_detail&section_id='
									. $post['section_id'] . '&showbuttons=1&section_name=' . $post['section_name'] . '&media_section='
									. $post['media_section'], $msg, 'warning'
								);
							}
							else
							{
								$this->setRedirect('index.php?option=com_redshop&view=media_detail', $msg, 'warning');
							}
						}
					}
				}
			}
		}

		if ($post['media_type'] == 'youtube')
		{
			$post['media_name'] = $post['youtube_id'];

			$link = 'index.php?option=com_redshop&view=media';

			if (isset($post['set']))
			{
				$link = 'index.php?option=com_redshop&view=media&tmpl=component';
			}
			
			if ($model->store($post))
			{
				$msg = JText::_('COM_REDSHOP_MEDIA_DETAIL_SAVED');
				$this->setRedirect($link, $msg, 'success');
			}
			else
			{
				

				$msg = JText::_('COM_REDSHOP_ERROR_SAVING_MEDIA_DETAIL');
				$this->setRedirect($link, $msg, 'warning');
			}
		}
	}
}
